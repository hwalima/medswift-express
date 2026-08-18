<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Operations Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['Active Shipments',  $stats['active'],          'text-teal dark:text-teal-light'],
                    ['Pending Pickup',    $stats['pending'],         'text-cyan dark:text-cyan'],
                    ['Exceptions',        $stats['exceptions'],      'text-red-600 dark:text-red-400'],
                    ['Delivered Today',   $stats['delivered_today'], 'text-emerald dark:text-emerald-light'],
                ] as [$label, $value, $color])
                    <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-bold {{ $color }}">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Flagged shipments --}}
                <div class="lg:col-span-2 bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🚨 Flagged / Urgent</h3>
                        <a href="{{ route('admin.shipments.index', ['priority' => 'urgent']) }}"
                           class="text-xs text-teal dark:text-teal-light hover:underline">View all →</a>
                    </div>
                    @if ($flagged->isEmpty())
                        <p class="px-6 py-10 text-sm text-slate dark:text-slate-light text-center">No flagged shipments.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-surface-dark text-xs font-semibold uppercase text-slate dark:text-slate-light tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Tracking #</th>
                                        <th class="px-6 py-3 text-left">Client</th>
                                        <th class="px-6 py-3 text-left">Flags</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($flagged as $s)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-surface-dark transition-colors">
                                            <td class="px-6 py-3 font-mono text-xs text-teal dark:text-teal-light">{{ $s->tracking_number }}</td>
                                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $s->client?->name }}</td>
                                            <td class="px-6 py-3 space-x-1">
                                                @if ($s->priority === 'urgent')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                                                @endif
                                                @if ($s->is_biohazard)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">☣</span>
                                                @endif
                                                @if ($s->current_status === 'exception')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Exception</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3"><x-status-badge :status="$s->current_status"/></td>
                                            <td class="px-6 py-3">
                                                <a href="{{ route('admin.shipments.show', $s) }}"
                                                   class="text-teal dark:text-teal-light text-xs hover:underline">Manage →</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Today's routes --}}
                <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Today's Routes</h3>
                    </div>
                    @if ($activeRoutes->isEmpty())
                        <p class="px-6 py-10 text-sm text-slate dark:text-slate-light text-center">No routes scheduled today.</p>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($activeRoutes as $route)
                                <li class="px-6 py-4">
                                    <p class="font-medium text-sm text-gray-800 dark:text-gray-100">{{ $route->route_name }}</p>
                                    <p class="text-xs text-slate dark:text-slate-light mt-0.5">{{ $route->driver?->name }} · {{ $route->shipments_count }} stop(s)</p>
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $route->status === 'active' ? 'bg-emerald/10 text-emerald dark:text-emerald-light' : 'bg-slate/10 text-slate-dark dark:text-slate-light' }}">
                                        {{ ucfirst($route->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>

            {{-- Recent shipments --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Recent Shipments</h3>
                    <a href="{{ route('admin.shipments.index') }}" class="text-xs text-teal dark:text-teal-light hover:underline">View all →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-surface-dark text-xs font-semibold uppercase text-slate dark:text-slate-light tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Tracking #</th>
                                <th class="px-6 py-3 text-left">Client</th>
                                <th class="px-6 py-3 text-left">Courier</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Booked</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($recentShipments as $s)
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-dark transition-colors">
                                    <td class="px-6 py-3 font-mono text-xs text-teal dark:text-teal-light">{{ $s->tracking_number }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $s->client?->name }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $s->courier?->name ?? '—' }}</td>
                                    <td class="px-6 py-3"><x-status-badge :status="$s->current_status"/></td>
                                    <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $s->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('admin.shipments.show', $s) }}"
                                           class="text-teal dark:text-teal-light text-xs hover:underline">Manage →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-sidebar-layout>