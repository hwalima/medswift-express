<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;

class AiToolsService
{
    // ─── Tool registry ────────────────────────────────────────────

    public function getToolDefinitions(User $user): array
    {
        $tools = [
            $this->defTrackShipment(),
            $this->defListShipments(),
            $this->defCalculateQuote(),
        ];

        if ($user->isAdmin()) {
            $tools[] = $this->defGetOperationsSummary();
            $tools[] = $this->defGetActiveExceptions();
        }

        return $tools;
    }

    public function execute(string $name, array $args, User $user): mixed
    {
        return match ($name) {
            'track_shipment'          => $this->trackShipment($args['tracking_number'] ?? '', $user),
            'list_shipments'          => $this->listShipments($user),
            'calculate_quote'         => $this->calculateQuote($args),
            'get_operations_summary'  => $this->getOperationsSummary($user),
            'get_active_exceptions'   => $this->getActiveExceptions($user),
            default                   => ['error' => "Unknown tool: {$name}"],
        };
    }

    // ─── Tool: track_shipment ─────────────────────────────────────

    private function defTrackShipment(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name'        => 'track_shipment',
                'description' => 'Track a shipment by its tracking number. Returns current status, location, temperature class, priority, assigned courier, and the last 5 status log entries.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'tracking_number' => [
                            'type'        => 'string',
                            'description' => 'The shipment tracking number, e.g. MS-A1B2C3D4',
                        ],
                    ],
                    'required' => ['tracking_number'],
                ],
            ],
        ];
    }

    public function trackShipment(string $trackingNumber, User $user): array
    {
        $query = Shipment::with(['client', 'courier', 'statusLogs' => fn ($q) => $q->limit(5)])
            ->where('tracking_number', strtoupper(trim($trackingNumber)));

        // Non-admins can only see their own shipments
        if (! $user->isAdmin()) {
            $query->where('client_id', $user->id);
        }

        $shipment = $query->first();

        if (! $shipment) {
            return ['found' => false, 'message' => "Shipment {$trackingNumber} not found or not accessible."];
        }

        return [
            'found'             => true,
            'tracking_number'   => $shipment->tracking_number,
            'current_status'    => $shipment->current_status,
            'status_label'      => $shipment->statusLabel(),
            'temperature_class' => $shipment->temperature_class,
            'priority'          => $shipment->priority,
            'is_biohazard'      => $shipment->is_biohazard,
            'origin'            => $shipment->origin_address,
            'destination'       => $shipment->destination_address,
            'courier'           => $shipment->courier?->name ?? 'Not yet assigned',
            'scheduled_pickup'  => $shipment->scheduled_pickup_at?->toDateTimeString(),
            'picked_up_at'      => $shipment->picked_up_at?->toDateTimeString(),
            'delivered_at'      => $shipment->delivered_at?->toDateTimeString(),
            'special_instructions' => $shipment->special_instructions,
            'status_timeline'   => $shipment->statusLogs->map(fn ($l) => [
                'status'    => $l->status,
                'location'  => $l->location,
                'notes'     => $l->notes,
                'temp_c'    => $l->temperature_reading,
                'logged_at' => $l->created_at->toDateTimeString(),
                'logged_by' => $l->logger?->name ?? 'System',
            ])->toArray(),
            'invoice_url' => url("/shipments/{$shipment->id}/invoice"),
        ];
    }

    // ─── Tool: list_shipments ─────────────────────────────────────

    private function defListShipments(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name'        => 'list_shipments',
                'description' => 'List the current user\'s shipments. Returns tracking numbers, statuses, and priority flags.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [],
                    'required'   => [],
                ],
            ],
        ];
    }

    public function listShipments(User $user): array
    {
        $query = $user->isAdmin()
            ? Shipment::query()
            : Shipment::where('client_id', $user->id);

        $shipments = $query->orderByDesc('created_at')->limit(10)->get();

        return [
            'count'     => $shipments->count(),
            'shipments' => $shipments->map(fn ($s) => [
                'tracking_number' => $s->tracking_number,
                'status'          => $s->statusLabel(),
                'priority'        => $s->priority,
                'temperature'     => $s->temperature_class,
                'is_biohazard'    => $s->is_biohazard,
                'booked'          => $s->created_at->toDateString(),
            ])->toArray(),
        ];
    }

    // ─── Tool: calculate_quote ────────────────────────────────────

    private function defCalculateQuote(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name'        => 'calculate_quote',
                'description' => 'Calculate a detailed shipping quote in ZAR for a medical courier job with itemised line items.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'origin'            => ['type' => 'string', 'description' => 'Pickup address or area'],
                        'destination'       => ['type' => 'string', 'description' => 'Delivery address or area'],
                        'temperature_class' => ['type' => 'string', 'enum' => ['ambient', 'refrigerated', 'frozen']],
                        'priority'          => ['type' => 'string', 'enum' => ['routine', 'urgent']],
                        'is_biohazard'      => ['type' => 'boolean', 'description' => 'Whether the sample is biohazardous'],
                    ],
                    'required' => ['origin', 'destination', 'temperature_class', 'priority'],
                ],
            ],
        ];
    }

    public function calculateQuote(array $args): array
    {
        $tempClass   = $args['temperature_class'] ?? 'ambient';
        $priority    = $args['priority'] ?? 'routine';
        $isBiohazard = (bool) ($args['is_biohazard'] ?? false);

        $base           = 280.00;
        $tempSurcharge  = match ($tempClass) { 'refrigerated' => 180.00, 'frozen' => 350.00, default => 0.00 };
        $urgentSurcharge = $priority === 'urgent' ? 450.00 : 0.00;
        $bioSurcharge   = $isBiohazard ? 220.00 : 0.00;

        $subtotal  = $base + $tempSurcharge + $urgentSurcharge + $bioSurcharge;
        $fuelLevy  = round($subtotal * 0.08, 2);
        $total     = round(($subtotal + $fuelLevy) * 1.15, 2); // +15% VAT

        $quoteRef = 'QT-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

        return [
            'quote_reference'  => $quoteRef,
            'valid_until'      => now()->addDays(7)->toDateString(),
            'origin'           => $args['origin'] ?? '',
            'destination'      => $args['destination'] ?? '',
            'temperature_class'  => $tempClass,
            'priority'          => $priority,
            'is_biohazard'      => $isBiohazard,
            'estimated_delivery' => $priority === 'urgent' ? 'Same day' : 'Next business day',
            'line_items' => array_filter([
                ['description' => 'Base medical courier rate',      'amount' => $base],
                $tempSurcharge  ? ['description' => ucfirst($tempClass) . ' cold-chain surcharge', 'amount' => $tempSurcharge]  : null,
                $urgentSurcharge ? ['description' => 'Urgent priority surcharge',                   'amount' => $urgentSurcharge] : null,
                $bioSurcharge   ? ['description' => 'Biohazard handling & compliance',              'amount' => $bioSurcharge]   : null,
                ['description' => 'Fuel levy (8%)',                 'amount' => $fuelLevy],
            ]),
            'subtotal_excl_vat' => round($subtotal + $fuelLevy, 2),
            'vat_15_percent'    => round(($subtotal + $fuelLevy) * 0.15, 2),
            'total_incl_vat'    => $total,
            'currency'          => 'ZAR',
        ];
    }

    // ─── Tool: get_operations_summary (admin) ─────────────────────

    private function defGetOperationsSummary(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name'        => 'get_operations_summary',
                'description' => 'Get a high-level operations summary for admin users: active shipments, delivery stats, courier load, and today\'s routes.',
                'parameters'  => ['type' => 'object', 'properties' => [], 'required' => []],
            ],
        ];
    }

    public function getOperationsSummary(User $user): array
    {
        abort_unless($user->isAdmin(), 403);

        return [
            'active_shipments'      => Shipment::whereNotIn('current_status', ['delivered', 'cancelled'])->count(),
            'pending_pickup'        => Shipment::where('current_status', 'pending')->count(),
            'in_transit'            => Shipment::where('current_status', 'in_transit')->count(),
            'delivered_today'       => Shipment::where('current_status', 'delivered')->whereDate('delivered_at', today())->count(),
            'exceptions_open'       => Shipment::where('current_status', 'exception')->count(),
            'urgent_open'           => Shipment::where('priority', 'urgent')->whereNotIn('current_status', ['delivered', 'cancelled'])->count(),
            'biohazard_in_transit'  => Shipment::where('is_biohazard', true)->whereNotIn('current_status', ['delivered', 'cancelled'])->count(),
            'total_shipments_ever'  => Shipment::count(),
        ];
    }

    // ─── Tool: get_active_exceptions (admin) ──────────────────────

    private function defGetActiveExceptions(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name'        => 'get_active_exceptions',
                'description' => 'Get all shipments currently in exception or delay status, with client details.',
                'parameters'  => ['type' => 'object', 'properties' => [], 'required' => []],
            ],
        ];
    }

    public function getActiveExceptions(User $user): array
    {
        abort_unless($user->isAdmin(), 403);

        $exceptions = Shipment::where('current_status', 'exception')
            ->with('client', 'courier')
            ->orderByDesc('updated_at')
            ->get();

        return [
            'count'      => $exceptions->count(),
            'exceptions' => $exceptions->map(fn ($s) => [
                'tracking_number' => $s->tracking_number,
                'client'          => $s->client?->name,
                'courier'         => $s->courier?->name ?? 'Unassigned',
                'priority'        => $s->priority,
                'is_biohazard'    => $s->is_biohazard,
                'temperature'     => $s->temperature_class,
                'last_updated'    => $s->updated_at->diffForHumans(),
            ])->toArray(),
        ];
    }
}
