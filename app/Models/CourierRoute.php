<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourierRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'route_name',
        'scheduled_date',
        'status',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at'     => 'datetime',
        'completed_at'   => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'courier_route_shipment')
                    ->withPivot('stop_order')
                    ->orderByPivot('stop_order');
    }
}
