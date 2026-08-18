@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => '', 'hint' => '', 'options' => []])

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
    </label>

    @if ($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600
                       dark:bg-surface-dark dark:text-gray-100
                       focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
            @foreach ($options as $optVal => $optLabel)
                <option value="{{ $optVal }}" {{ $value === (string) $optVal ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
    @elseif ($type === 'password')
        <input type="password" id="{{ $name }}" name="{{ $name }}"
               placeholder="{{ $hint ? 'Enter new value…' : $placeholder }}"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600
                      dark:bg-surface-dark dark:text-gray-100
                      focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
    @else
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
               value="{{ $value }}" placeholder="{{ $placeholder }}"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600
                      dark:bg-surface-dark dark:text-gray-100
                      focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
    @endif

    @if ($hint)
        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $hint }}</p>
    @endif
</div>
