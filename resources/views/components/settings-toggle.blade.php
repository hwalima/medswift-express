@props(['name', 'label', 'checked' => false])

<div class="sm:col-span-2">
    <label class="flex items-center gap-3 cursor-pointer group">
        <div class="relative">
            <input type="checkbox" name="{{ $name }}" value="1"
                   class="sr-only peer" {{ $checked ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 rounded-full
                        peer-checked:bg-teal transition-colors
                        peer-focus:ring-2 peer-focus:ring-teal/30"></div>
            <div class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 shadow
                        transition-transform peer-checked:translate-x-5"></div>
        </div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
            {{ $label }}
        </span>
    </label>
</div>
