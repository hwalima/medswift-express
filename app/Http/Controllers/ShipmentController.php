<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        $stats = [
            'active'    => $user->clientShipments()->whereNotIn('current_status', ['delivered', 'cancelled'])->count(),
            'delivered' => $user->clientShipments()->where('current_status', 'delivered')->count(),
            'total'     => $user->clientShipments()->count(),
        ];

        $recent = $user->clientShipments()->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard', compact('stats', 'recent'));
    }

    public function index(): View
    {
        $shipments = auth()->user()
            ->clientShipments()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('shipments.index', compact('shipments'));
    }

    public function create(): View
    {
        return view('shipments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'origin_address'       => 'required|string|max:500',
            'destination_address'  => 'required|string|max:500',
            'temperature_class'    => 'required|in:ambient,refrigerated,frozen',
            'priority'             => 'required|in:routine,urgent',
            'is_biohazard'         => 'nullable|boolean',
            'special_instructions' => 'nullable|string|max:1000',
            'scheduled_pickup_at'  => 'nullable|date|after:now',
        ]);

        $shipment = Shipment::create([
            ...$data,
            'client_id'       => auth()->id(),
            'tracking_number' => Shipment::generateTrackingNumber(),
            'current_status'  => 'pending',
            'is_biohazard'    => $request->boolean('is_biohazard'),
        ]);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Shipment booked! Tracking number: ' . $shipment->tracking_number);
    }

    public function show(Shipment $shipment): View
    {
        abort_unless($shipment->client_id === auth()->id(), 403);

        $shipment->load(['statusLogs.logger', 'courier']);

        return view('shipments.show', compact('shipment'));
    }

    public function invoice(Shipment $shipment): View
    {
        // Admins or the owning client can view invoices
        abort_unless($shipment->client_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $shipment->load(['client', 'courier']);

        $invoiceNumber  = 'INV-' . $shipment->id . '-' . $shipment->created_at->format('Y');
        $base           = 280.00;
        $tempSurcharge  = match ($shipment->temperature_class) { 'refrigerated' => 180.00, 'frozen' => 350.00, default => 0.00 };
        $urgentSurcharge = $shipment->priority === 'urgent' ? 450.00 : 0.00;
        $bioSurcharge   = $shipment->is_biohazard ? 220.00 : 0.00;
        $subtotal       = $base + $tempSurcharge + $urgentSurcharge + $bioSurcharge;
        $fuelLevy       = round($subtotal * 0.08, 2);
        $subtotalExclVat = round($subtotal + $fuelLevy, 2);
        $vat            = round($subtotalExclVat * 0.15, 2);
        $total          = round($subtotalExclVat + $vat, 2);

        $lineItems = array_values(array_filter([
            ['description' => 'Base medical courier rate',                 'amount' => $base],
            $tempSurcharge   ? ['description' => ucfirst($shipment->temperature_class) . ' cold-chain surcharge', 'amount' => $tempSurcharge]   : null,
            $urgentSurcharge ? ['description' => 'Urgent priority surcharge',                                      'amount' => $urgentSurcharge] : null,
            $bioSurcharge    ? ['description' => 'Biohazard handling & compliance',                                'amount' => $bioSurcharge]    : null,
            ['description' => 'Fuel levy (8%)',                            'amount' => $fuelLevy],
        ]));

        return view('shipments.invoice', compact('shipment', 'invoiceNumber', 'lineItems', 'subtotalExclVat', 'vat', 'total'));
    }
}
