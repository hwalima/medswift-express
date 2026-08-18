@props(['status'])

@php
$cfg = match($status) {
    'pending'              => ['bg-slate/10 text-slate-dark dark:bg-slate/20 dark:text-slate-light',       'Pending'],
    'picked_up'            => ['bg-cyan/10 text-cyan-dark dark:bg-cyan/20 dark:text-cyan',                 'Picked Up'],
    'cold_chain_validated' => ['bg-teal/10 text-teal-dark dark:bg-teal/20 dark:text-teal-light',           'Cold Chain ✓'],
    'in_transit'           => ['bg-teal/20 text-teal dark:bg-teal/30 dark:text-teal-light',                'In Transit'],
    'lab_arrived'          => ['bg-emerald/10 text-emerald-dark dark:bg-emerald/20 dark:text-emerald',     'Lab Arrived'],
    'delivered'            => ['bg-emerald/20 text-emerald-dark dark:bg-emerald/30 dark:text-emerald',     'Delivered'],
    'exception'            => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',             'Exception'],
    'cancelled'            => ['bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400',         'Cancelled'],
    default                => ['bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400',         ucfirst(str_replace('_', ' ', $status))],
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cfg[0] }}">
    {{ $cfg[1] }}
</span>
