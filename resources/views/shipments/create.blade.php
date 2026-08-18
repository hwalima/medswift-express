<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-slate dark:text-slate-light hover:text-teal dark:hover:text-teal-light text-sm">← Dashboard</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100">Book a Pickup</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 sm:p-8">

                <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
                    @csrf

                    {{-- Addresses --}}
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="origin_address" value="Origin Address"/>
                            <x-text-input id="origin_address" name="origin_address" type="text"
                                          class="mt-1 block w-full" required
                                          value="{{ old('origin_address') }}"
                                          placeholder="Lab / clinic pickup address"/>
                            <x-input-error :messages="$errors->get('origin_address')" class="mt-1"/>
                        </div>
                        <div>
                            <x-input-label for="destination_address" value="Destination Address"/>
                            <x-text-input id="destination_address" name="destination_address" type="text"
                                          class="mt-1 block w-full" required
                                          value="{{ old('destination_address') }}"
                                          placeholder="Receiving lab / facility address"/>
                            <x-input-error :messages="$errors->get('destination_address')" class="mt-1"/>
                        </div>
                    </div>

                    {{-- Temperature class --}}
                    <div>
                        <x-input-label value="Temperature Class"/>
                        <div class="mt-2 grid grid-cols-3 gap-3">
                            @foreach ([
                                'ambient'      => ['🌡', 'Ambient', 'Room temperature'],
                                'refrigerated' => ['❄', 'Refrigerated', '2–8 °C'],
                                'frozen'       => ['🧊', 'Frozen', '−20 °C or below'],
                            ] as $value => [$icon, $label, $sub])
                                <label class="cursor-pointer">
                                    <input type="radio" name="temperature_class" value="{{ $value }}"
                                           class="sr-only peer"
                                           {{ old('temperature_class', 'ambient') === $value ? 'checked' : '' }}>
                                    <div class="rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 text-center
                                                peer-checked:border-teal peer-checked:bg-teal/5
                                                dark:peer-checked:border-teal-light dark:peer-checked:bg-teal/10
                                                hover:border-teal/50 transition-colors">
                                        <span class="text-xl">{{ $icon }}</span>
                                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $label }}</p>
                                        <p class="text-xs text-slate dark:text-slate-light">{{ $sub }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('temperature_class')" class="mt-1"/>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <x-input-label value="Priority"/>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            @foreach (['routine' => ['📦', 'Routine'], 'urgent' => ['🚨', 'Urgent']] as $value => [$icon, $label])
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="{{ $value }}"
                                           class="sr-only peer"
                                           {{ old('priority', 'routine') === $value ? 'checked' : '' }}>
                                    <div class="rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 text-center
                                                peer-checked:border-teal peer-checked:bg-teal/5
                                                dark:peer-checked:border-teal-light dark:peer-checked:bg-teal/10
                                                hover:border-teal/50 transition-colors">
                                        <span class="text-xl">{{ $icon }}</span>
                                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $label }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Scheduled pickup --}}
                    <div>
                        <x-input-label for="scheduled_pickup_at" value="Scheduled Pickup (optional)"/>
                        <x-text-input id="scheduled_pickup_at" name="scheduled_pickup_at" type="datetime-local"
                                      class="mt-1 block w-full"
                                      value="{{ old('scheduled_pickup_at') }}"/>
                        <x-input-error :messages="$errors->get('scheduled_pickup_at')" class="mt-1"/>
                    </div>

                    {{-- Biohazard + special instructions --}}
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_biohazard" value="1"
                                   class="rounded border-gray-300 text-teal focus:ring-teal"
                                   {{ old('is_biohazard') ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="text-red-500">☣</span> Mark as Biohazardous
                            </span>
                        </label>
                        <div>
                            <x-input-label for="special_instructions" value="Special Instructions (optional)"/>
                            <textarea id="special_instructions" name="special_instructions" rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600
                                             dark:bg-surface-dark dark:text-gray-100
                                             focus:border-teal focus:ring-teal/50 text-sm"
                                      placeholder="Handle with care, keep upright…">{{ old('special_instructions') }}</textarea>
                            <x-input-error :messages="$errors->get('special_instructions')" class="mt-1"/>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard') }}"
                           class="rounded-lg px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300
                                  border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </a>
                        <x-primary-button class="bg-teal hover:bg-teal-dark focus:ring-teal">
                            Confirm Booking
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-sidebar-layout>