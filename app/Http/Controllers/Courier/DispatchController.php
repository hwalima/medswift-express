<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        abort_unless($shipment->courier_id === auth()->id(), 403);

        $data = $request->validate([
            'status'              => 'required|in:picked_up,cold_chain_validated,in_transit,lab_arrived,delivered,exception',
            'location'            => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:500',
            'temperature_reading' => 'nullable|numeric|between:-100,100',
        ]);

        $timestamps = match ($data['status']) {
            'picked_up' => ['picked_up_at' => now()],
            'delivered' => ['delivered_at' => now()],
            default     => [],
        };

        $shipment->update(array_merge(['current_status' => $data['status']], $timestamps));

        ShipmentStatusLog::create([
            'shipment_id'         => $shipment->id,
            'logged_by'           => auth()->id(),
            'status'              => $data['status'],
            'location'            => $data['location'] ?? null,
            'notes'               => $data['notes'] ?? null,
            'temperature_reading' => $data['temperature_reading'] ?? null,
        ]);

        return back()->with('success', 'Status updated to: ' . ucfirst(str_replace('_', ' ', $data['status'])));
    }
}
