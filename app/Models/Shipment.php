<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_number',
        'client_id',
        'courier_id',
        'origin_address',
        'destination_address',
        'temperature_class',
        'priority',
        'current_status',
        'is_biohazard',
        'special_instructions',
        'proof_of_delivery_path',
        'scheduled_pickup_at',
        'picked_up_at',
        'delivered_at',
    ];

    protected $casts = [
        'is_biohazard'        => 'boolean',
        'scheduled_pickup_at' => 'datetime',
        'picked_up_at'        => 'datetime',
        'delivered_at'        => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ShipmentStatusLog::class)->latest();
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(CourierRoute::class, 'courier_route_shipment')
                    ->withPivot('stop_order');
    }

    public function statusLabel(): string
    {
        return match ($this->current_status) {
            'pending'              => 'Pending',
            'picked_up'            => 'Picked Up',
            'cold_chain_validated' => 'Cold Chain Validated',
            'in_transit'           => 'In Transit',
            'lab_arrived'          => 'Lab Arrived',
            'delivered'            => 'Delivered',
            'exception'            => 'Exception / Delay',
            'cancelled'            => 'Cancelled',
            default                => ucfirst(str_replace('_', ' ', $this->current_status)),
        };
    }

    public function isActive(): bool
    {
        return ! in_array($this->current_status, ['delivered', 'cancelled']);
    }

    public static function generateTrackingNumber(): string
    {
        do {
            $candidate = 'MS-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        } while (static::where('tracking_number', $candidate)->exists());

        return $candidate;
    }
}
