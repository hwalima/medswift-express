<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Dispatch Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Pending Pickups</p>
                    <p class="mt-2 text-3xl font-bold text-cyan dark:text-cyan">{{ $stats['pending_pickups'] }}</p>
                </div>
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">In Transit</p>
                    <p class="mt-2 text-3xl font-bold text-teal dark:text-teal-light">{{ $stats['in_transit'] }}</p>
                </div>
                <div class="bg-white dark:bg-surface-dark-card rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/50">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate dark:text-slate-light">Delivered Today</p>
                    <p class="mt-2 text-3xl font-bold text-emerald dark:text-emerald-light">{{ $stats['delivered_today'] }}</p>
                </div>
            </div>

            {{-- Today's route --}}
            @if ($todayRoute)
                <div class="bg-teal/5 dark:bg-teal/10 border border-teal/20 rounded-xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-teal dark:text-teal-light">Today's Route</p>
                    <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $todayRoute->route_name }}</p>
                    <p class="text-xs text-slate dark:text-slate-light mt-0.5">
                        {{ $todayRoute->shipments->count() }} stop(s) ·
                        <span class="capitalize">{{ $todayRoute->status }}</span>
                    </p>
                </div>
            @endif

            {{-- Assigned shipments --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Assigned Shipments</h3>
                </div>

                @if ($assignedShipments->isEmpty())
                    <p class="px-6 py-12 text-sm text-slate dark:text-slate-light text-center">No shipments assigned to you.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($assignedShipments as $shipment)
                            <div class="px-6 py-5" x-data="{ open: false }">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono font-medium text-teal dark:text-teal-light text-sm">
                                                {{ $shipment->tracking_number }}
                                            </span>
                                            @if ($shipment->priority === 'urgent')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">🚨 Urgent</span>
                                            @endif
                                            @if ($shipment->is_biohazard)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">☣</span>
                                            @endif
                                            <x-status-badge :status="$shipment->current_status"/>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            {{ $shipment->origin_address }} → {{ $shipment->destination_address }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate dark:text-slate-light capitalize">
                                            {{ $shipment->temperature_class }}
                                            @if ($shipment->scheduled_pickup_at)
                                                · Pickup: {{ $shipment->scheduled_pickup_at->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    <button @click="open = !open"
                                            class="text-sm rounded-lg border border-teal text-teal dark:border-teal-light dark:text-teal-light
                                                   px-3 py-1.5 hover:bg-teal hover:text-white dark:hover:bg-teal dark:hover:text-white
                                                   transition-colors shrink-0">
                                        Update Status
                                    </button>
                                </div>

                                {{-- Inline status update form --}}
                                <div x-show="open" x-collapse class="mt-4">
                                    <form method="POST"
                                          action="{{ route('courier.shipments.update-status', $shipment) }}"
                                          class="bg-gray-50 dark:bg-surface-dark rounded-lg p-4 space-y-3">
                                        @csrf
                                        @method('PATCH')

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <x-input-label for="status_{{ $shipment->id }}" value="New Status"/>
                                                <select id="status_{{ $shipment->id }}" name="status"
                                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600
                                                               dark:bg-surface-dark dark:text-gray-100
                                                               focus:border-teal focus:ring-teal/50 text-sm">
                                                    @foreach ([
                                                        'picked_up'            => 'Picked Up',
                                                        'cold_chain_validated' => 'Cold Chain Validated',
                                                        'in_transit'           => 'In Transit',
                                                        'lab_arrived'          => 'Lab Arrived',
                                                        'delivered'            => 'Delivered',
                                                        'exception'            => 'Exception / Delay',
                                                    ] as $val => $lbl)
                                                        <option value="{{ $val }}" {{ $shipment->current_status === $val ? 'selected' : '' }}>
                                                            {{ $lbl }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label for="location_{{ $shipment->id }}" value="Current Location (optional)"/>
                                                <x-text-input id="location_{{ $shipment->id }}" name="location"
                                                              type="text" class="mt-1 block w-full"
                                                              placeholder="e.g. Johannesburg North Depot"/>
                                            </div>
                                            <div>
                                                <x-input-label for="temp_{{ $shipment->id }}" value="Temp Reading °C (optional)"/>
                                                <x-text-input id="temp_{{ $shipment->id }}" name="temperature_reading"
                                                              type="number" step="0.1" class="mt-1 block w-full"
                                                              placeholder="e.g. 4.2"/>
                                            </div>
                                            <div>
                                                <x-input-label for="notes_{{ $shipment->id }}" value="Notes (optional)"/>
                                                <x-text-input id="notes_{{ $shipment->id }}" name="notes"
                                                              type="text" class="mt-1 block w-full"
                                                              placeholder="e.g. Handed to reception"/>
                                            </div>
                                        </div>

                                        <div class="flex justify-end">
                                            <x-primary-button class="bg-teal hover:bg-teal-dark focus:ring-teal">
                                                Save Status
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
