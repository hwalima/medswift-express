<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\CourierRoute;
use App\Models\Shipment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $driver = auth()->user();

        $stats = [
            'pending_pickups' => $driver->courierShipments()->where('current_status', 'pending')->count(),
            'in_transit'      => $driver->courierShipments()->where('current_status', 'in_transit')->count(),
            'delivered_today' => $driver->courierShipments()
                ->where('current_status', 'delivered')
                ->whereDate('delivered_at', today())
                ->count(),
        ];

        $todayRoute = CourierRoute::where('driver_id', $driver->id)
            ->whereIn('status', ['scheduled', 'active'])
            ->whereDate('scheduled_date', today())
            ->with(['shipments' => fn ($q) => $q->orderByPivot('stop_order')])
            ->first();

        $assignedShipments = $driver->courierShipments()
            ->whereNotIn('current_status', ['delivered', 'cancelled'])
            ->orderByRaw("priority = 'urgent' DESC")
            ->orderBy('scheduled_pickup_at')
            ->get();

        return view('courier.dashboard', compact('stats', 'todayRoute', 'assignedShipments'));
    }
}
