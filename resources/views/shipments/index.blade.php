<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">My Shipments</h2>
            <a href="{{ route('shipments.create') }}"
               class="rounded-lg bg-teal px-4 py-2 text-sm font-medium text-white hover:bg-teal-dark transition-colors">
                + Book Pickup
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                @if ($shipments->isEmpty())
                    <div class="px-6 py-16 text-center text-slate dark:text-slate-light text-sm">
                        No shipments found.
                        <a href="{{ route('shipments.create') }}" class="text-teal dark:text-teal-light underline ml-1">Book your first pickup.</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-surface-dark text-xs font-semibold uppercase text-slate dark:text-slate-light tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tracking #</th>
                                    <th class="px-6 py-3 text-left">Origin → Destination</th>
                                    <th class="px-6 py-3 text-left">Temp Class</th>
                                    <th class="px-6 py-3 text-left">Priority</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($shipments as $shipment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-surface-dark transition-colors">
                                        <td class="px-6 py-4 font-mono font-medium text-teal dark:text-teal-light whitespace-nowrap">
                                            {{ $shipment->tracking_number }}
                                            @if ($shipment->is_biohazard)
                                                <span class="ml-1 text-xs text-red-500" title="Biohazard">☣</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 max-w-xs">
                                            <div class="truncate">{{ $shipment->origin_address }}</div>
                                            <div class="truncate text-xs text-slate dark:text-slate-light">→ {{ $shipment->destination_address }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs capitalize text-gray-600 dark:text-gray-400">
                                            {{ $shipment->temperature_class }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($shipment->priority === 'urgent')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                                            @else
                                                <span class="text-slate dark:text-slate-light text-xs">Routine</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4"><x-status-badge :status="$shipment->current_status"/></td>
                                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $shipment->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('shipments.show', $shipment) }}"
                                               class="text-teal dark:text-teal-light text-xs hover:underline">Track →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $shipments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-layout>