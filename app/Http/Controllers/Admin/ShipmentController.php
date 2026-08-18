<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentStatusLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shipment::with('client', 'courier');

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $query->where('tracking_number', 'like', '%' . $request->search . '%');
        }

        $shipments = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $couriers  = User::where('role', 'courier')->where('is_active', true)->orderBy('name')->get();

        return view('admin.shipments.index', compact('shipments', 'couriers'));
    }

    public function show(Shipment $shipment): View
    {
        $shipment->load(['client', 'courier', 'statusLogs.logger']);
        $couriers = User::where('role', 'courier')->where('is_active', true)->orderBy('name')->get();

        return view('admin.shipments.show', compact('shipment', 'couriers'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'current_status' => 'required|in:pending,picked_up,cold_chain_validated,in_transit,lab_arrived,delivered,exception,cancelled',
            'courier_id'     => 'nullable|exists:users,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $oldStatus = $shipment->current_status;
        $shipment->update($data);

        if ($oldStatus !== $data['current_status']) {
            ShipmentStatusLog::create([
                'shipment_id' => $shipment->id,
                'logged_by'   => auth()->id(),
                'status'      => $data['current_status'],
                'notes'       => $data['notes'] ?? 'Status updated by admin.',
            ]);
        }

        return back()->with('success', 'Shipment updated.');
    }
}
