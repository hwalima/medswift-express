<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">
                Welcome back, {{ auth()->user()->name }}
            </h2>
            <a href="{{ route('shipments.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-teal px-4 py-2 text-sm font-medium text-white
                      hover:bg-teal-dark transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Book Pickup
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Active Shipments</p>
                    <p class="mt-2 text-3xl font-bold text-teal dark:text-teal-light">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Delivered</p>
                    <p class="mt-2 text-3xl font-bold text-emerald dark:text-emerald-light">{{ $stats['delivered'] }}</p>
                </div>
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Total Shipments</p>
                    <p class="mt-2 text-3xl font-bold text-gray-700 dark:text-gray-200">{{ $stats['total'] }}</p>
                </div>
            </div>

            {{-- Recent shipments --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Recent Shipments</h3>
                    <a href="{{ route('shipments.index') }}" class="text-sm text-teal dark:text-teal-light hover:underline">View all →</a>
                </div>

                @if ($recent->isEmpty())
                    <div class="px-6 py-12 text-center text-slate dark:text-slate-light text-sm">
                        No shipments yet.
                        <a href="{{ route('shipments.create') }}" class="text-teal dark:text-teal-light underline ml-1">Book your first pickup.</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-surface-dark text-xs font-semibold uppercase text-slate dark:text-slate-light tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tracking #</th>
                                    <th class="px-6 py-3 text-left">Destination</th>
                                    <th class="px-6 py-3 text-left">Priority</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Booked</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($recent as $shipment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-surface-dark transition-colors">
                                        <td class="px-6 py-4 font-mono font-medium text-teal dark:text-teal-light">
                                            {{ $shipment->tracking_number }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                            {{ $shipment->destination_address }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($shipment->priority === 'urgent')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                                            @else
                                                <span class="text-slate dark:text-slate-light text-xs">Routine</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-status-badge :status="$shipment->current_status"/>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $shipment->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('shipments.show', $shipment) }}"
                                               class="text-teal dark:text-teal-light text-xs hover:underline">Track →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>