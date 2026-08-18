<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.shipments.index') }}" class="text-slate dark:text-slate-light hover:text-teal dark:hover:text-teal-light text-sm">← All Shipments</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <span class="font-mono text-sm text-teal dark:text-teal-light">{{ $shipment->tracking_number }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Details card --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <div class="flex flex-wrap justify-between gap-4 mb-5">
                    <div>
                        <x-status-badge :status="$shipment->current_status"/>
                        @if ($shipment->priority === 'urgent')
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                        @endif
                        @if ($shipment->is_biohazard)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">☣ Biohazard</span>
                        @endif
                    </div>
                    <div class="text-right text-xs text-slate dark:text-slate-light">
                        Booked {{ $shipment->created_at->diffForHumans() }}
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Client</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $shipment->client?->name }}</p>
                        <p class="text-xs text-slate dark:text-slate-light">{{ $shipment->client?->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate dark:text-slate-light uppercase tracking-wider mb-1">Courier</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $shipment->courier?->name ?? 'Not assigned' }}</p>
                    </div>
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
                </div>
            </div>

            {{-- Admin update form --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Shipment</h3>

                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-emerald/10 border border-emerald text-emerald-dark dark:text-emerald-light px-4 py-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="current_status" value="Status"/>
                            <select id="current_status" name="current_status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600
                                           dark:bg-surface-dark dark:text-gray-100
                                           focus:border-teal focus:ring-teal/50 text-sm">
                                @foreach (['pending','picked_up','cold_chain_validated','in_transit','lab_arrived','delivered','exception','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $shipment->current_status === $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="courier_id" value="Assign Courier"/>
                            <select id="courier_id" name="courier_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600
                                           dark:bg-surface-dark dark:text-gray-100
                                           focus:border-teal focus:ring-teal/50 text-sm">
                                <option value="">— Unassigned —</option>
                                @foreach ($couriers as $courier)
                                    <option value="{{ $courier->id }}" {{ $shipment->courier_id == $courier->id ? 'selected' : '' }}>
                                        {{ $courier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="notes" value="Admin Notes"/>
                            <x-text-input id="notes" name="notes" type="text" class="mt-1 block w-full"
                                          placeholder="Reason for status change…"/>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-teal hover:bg-teal-dark focus:ring-teal">Save Changes</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Status timeline --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Status History</h3>
                </div>
                @if ($shipment->statusLogs->isEmpty())
                    <p class="px-6 py-8 text-sm text-slate dark:text-slate-light text-center">No status logs yet.</p>
                @else
                    <div class="px-6 py-4 space-y-4">
                        @foreach ($shipment->statusLogs as $log)
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="h-3 w-3 rounded-full bg-teal dark:bg-teal-light mt-1 shrink-0"></div>
                                    @if (! $loop->last)<div class="w-px flex-1 bg-gray-200 dark:bg-gray-700 mt-1"></div>@endif
                                </div>
                                <div class="pb-4 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-status-badge :status="$log->status"/>
                                        <span class="text-xs text-slate dark:text-slate-light">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if ($log->location)<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">📍 {{ $log->location }}</p>@endif
                                    @if ($log->notes)<p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $log->notes }}</p>@endif
                                    @if ($log->temperature_reading !== null)<p class="mt-1 text-xs text-cyan">🌡 {{ $log->temperature_reading }}°C</p>@endif
                                    <p class="mt-1 text-xs text-slate dark:text-slate-light">by {{ $log->logger?->name ?? 'System' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
