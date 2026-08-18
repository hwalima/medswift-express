<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierRoute;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'active'          => Shipment::whereNotIn('current_status', ['delivered', 'cancelled'])->count(),
            'pending'         => Shipment::where('current_status', 'pending')->count(),
            'exceptions'      => Shipment::where('current_status', 'exception')->count(),
            'delivered_today' => Shipment::where('current_status', 'delivered')
                ->whereDate('delivered_at', today())->count(),
        ];

        $flagged = Shipment::where(function ($q) {
            $q->where('current_status', 'exception')
              ->orWhere(function ($q2) {
                  $q2->where('priority', 'urgent')
                     ->whereNotIn('current_status', ['delivered', 'cancelled']);
              })
              ->orWhere(function ($q2) {
                  $q2->where('is_biohazard', true)
                     ->whereNotIn('current_status', ['delivered', 'cancelled']);
              });
        })->with('client')->orderByDesc('updated_at')->limit(10)->get();

        $activeRoutes = CourierRoute::whereIn('status', ['scheduled', 'active'])
            ->whereDate('scheduled_date', today())
            ->with('driver')
            ->withCount('shipments')
            ->get();

        $recentShipments = Shipment::with('client', 'courier')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'flagged', 'activeRoutes', 'recentShipments'));
    }
}
