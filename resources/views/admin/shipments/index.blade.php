<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">All Shipments</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.shipments.index') }}"
                  class="flex flex-wrap gap-3 bg-white dark:bg-surface-dark-card rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700/50">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tracking number…"
                       class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-surface-dark dark:text-gray-100
                              focus:border-teal focus:ring-teal/50 text-sm px-3 py-2 w-48">
                <select name="status"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-surface-dark dark:text-gray-100
                               focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
                    <option value="">All Statuses</option>
                    @foreach (['pending','picked_up','cold_chain_validated','in_transit','lab_arrived','delivered','exception','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
                <select name="priority"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-surface-dark dark:text-gray-100
                               focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
                    <option value="">All Priorities</option>
                    <option value="routine"  {{ request('priority') === 'routine'  ? 'selected' : '' }}>Routine</option>
                    <option value="urgent"   {{ request('priority') === 'urgent'   ? 'selected' : '' }}>Urgent</option>
                </select>
                <button type="submit"
                        class="rounded-lg bg-teal text-white px-4 py-2 text-sm font-medium hover:bg-teal-dark transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['search','status','priority']))
                    <a href="{{ route('admin.shipments.index') }}"
                       class="rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300
                              px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Table --}}
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-surface-dark text-xs font-semibold uppercase text-slate dark:text-slate-light tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Tracking #</th>
                                <th class="px-6 py-3 text-left">Client</th>
                                <th class="px-6 py-3 text-left">Courier</th>
                                <th class="px-6 py-3 text-left">Temp / Priority</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Booked</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($shipments as $s)
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-dark transition-colors">
                                    <td class="px-6 py-3 font-mono text-xs text-teal dark:text-teal-light whitespace-nowrap">
                                        {{ $s->tracking_number }}
                                        @if ($s->is_biohazard)<span class="text-red-500 ml-1" title="Biohazard">☣</span>@endif
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $s->client?->name }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $s->courier?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-xs">
                                        <span class="capitalize text-gray-600 dark:text-gray-400">{{ $s->temperature_class }}</span>
                                        @if ($s->priority === 'urgent')
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Urgent</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3"><x-status-badge :status="$s->current_status"/></td>
                                    <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $s->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('admin.shipments.show', $s) }}"
                                           class="text-teal dark:text-teal-light text-xs hover:underline whitespace-nowrap">Manage →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate dark:text-slate-light text-sm">
                                        No shipments match your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $shipments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-sidebar-layout>