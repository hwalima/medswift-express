<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="text-slate dark:text-slate-light hover:text-teal dark:hover:text-teal-light text-sm">← My Shipments</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <span class="font-mono font-semibold text-teal dark:text-teal-light text-sm">{{ $shipment->tracking_number }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Status card --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Current Status</p>
                        <div class="mt-2 flex items-center gap-3">
                            <x-status-badge :status="$shipment->current_status"/>
                            @if ($shipment->priority === 'urgent')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                            @endif
                            @if ($shipment->is_biohazard)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">☣ Biohazard</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate dark:text-slate-light">Tracking Number</p>
                        <p class="font-mono font-bold text-teal dark:text-teal-light">{{ $shipment->tracking_number }}</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Origin</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $shipment->origin_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Destination</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $shipment->destination_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Temperature Class</p>
                        <p class="capitalize text-gray-700 dark:text-gray-300">{{ $shipment->temperature_class }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Assigned Courier</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $shipment->courier?->name ?? 'Not yet assigned' }}</p>
                    </div>
                    @if ($shipment->scheduled_pickup_at)
                        <div>
                            <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Scheduled Pickup</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $shipment->scheduled_pickup_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if ($shipment->delivered_at)
                        <div>
                            <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Delivered At</p>
                            <p class="text-emerald dark:text-emerald-light font-medium">{{ $shipment->delivered_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if ($shipment->special_instructions)
                    <div class="mt-4 p-3 rounded-lg bg-teal/5 dark:bg-teal/10 border border-teal/20 text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-teal dark:text-teal-light">Special Instructions:</span>
                        {{ $shipment->special_instructions }}
                    </div>
                @endif
            </div>

            {{-- Status timeline --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Shipment Timeline</h3>
                </div>

                @if ($shipment->statusLogs->isEmpty())
                    <p class="px-6 py-8 text-sm text-slate dark:text-slate-light text-center">No status updates yet.</p>
                @else
                    <div class="px-6 py-4 space-y-4">
                        @foreach ($shipment->statusLogs as $log)
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="h-3 w-3 rounded-full bg-teal dark:bg-teal-light mt-1 shrink-0"></div>
                                    @if (! $loop->last)
                                        <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700 mt-1"></div>
                                    @endif
                                </div>
                                <div class="pb-4 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-status-badge :status="$log->status"/>
                                        <span class="text-xs text-slate dark:text-slate-light">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if ($log->location)
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">📍 {{ $log->location }}</p>
                                    @endif
                                    @if ($log->notes)
                                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $log->notes }}</p>
                                    @endif
                                    @if ($log->temperature_reading !== null)
                                        <p class="mt-1 text-xs text-cyan dark:text-cyan">🌡 {{ $log->temperature_reading }}°C</p>
                                    @endif
                                    <p class="mt-1 text-xs text-slate dark:text-slate-light">by {{ $log->logger?->name ?? 'System' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-sidebar-layout>