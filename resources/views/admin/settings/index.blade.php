<x-sidebar-layout>
    @section('title', 'Settings — MedSwift Express')
    @section('page-title', 'Settings')

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Tab navigation --}}
        @php
        $tabs = [
            'general'  => ['General',    '🏢'],
            'branding' => ['Branding',   '🎨'],
            'email'    => ['Email/SMTP', '📧'],
            'whatsapp' => ['WhatsApp',   '💬'],
            'ai'       => ['AI & Swiftie','🤖'],
            'payments' => ['Payments',   '💳'],
        ];
        @endphp

        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-0">
            @foreach ($tabs as $key => [$label, $emoji])
                <a href="{{ route('admin.settings.group', $key) }}"
                   class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-colors
                          {{ $group === $key
                              ? 'border-teal text-teal dark:text-teal-light bg-teal/5'
                              : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300' }}">
                    <span>{{ $emoji }}</span> {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-lg bg-emerald/10 border border-emerald text-emerald-dark dark:text-emerald-light px-4 py-3 text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif

        {{-- Settings form --}}
        <form method="POST"
              action="{{ route('admin.settings.update', $group) }}"
              enctype="multipart/form-data"
              class="bg-white dark:bg-surface-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            @csrf
            @method('PATCH')

            <div class="p-6 space-y-6">

                {{-- ── GENERAL ─────────────────────────────── --}}
                @if ($group === 'general')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">General Settings</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-settings-field name="app_name" label="Application Name" :value="$settings['app_name'] ?? 'MedSwift Express'"/>
                        <x-settings-field name="app_tagline" label="Tagline" :value="$settings['app_tagline'] ?? ''"/>
                        <x-settings-field name="timezone" label="Timezone" type="select" :value="$settings['timezone'] ?? 'Africa/Johannesburg'"
                            :options="collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz])->toArray()"/>
                        <x-settings-field name="currency" label="Currency Code" placeholder="ZAR" :value="$settings['currency'] ?? 'ZAR'"/>
                        <x-settings-field name="currency_symbol" label="Currency Symbol" placeholder="R" :value="$settings['currency_symbol'] ?? 'R'"/>
                        <x-settings-field name="country" label="Country Code" placeholder="ZA" :value="$settings['country'] ?? 'ZA'"/>
                    </div>
                @endif

                {{-- ── BRANDING ─────────────────────────────── --}}
                @if ($group === 'branding')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">Branding & Assets</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                            @if (!empty($settings['logo_path']))
                                <img src="{{ $settings['logo_path'] }}" alt="Logo" class="h-12 w-auto mb-2 rounded">
                            @endif
                            <input type="file" name="logo_path" accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-medium file:bg-teal/10 file:text-teal dark:file:text-teal-light
                                          hover:file:bg-teal/20 transition-colors cursor-pointer">
                            <p class="text-xs text-gray-400">PNG or SVG recommended. Max 2MB.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Favicon</label>
                            @if (!empty($settings['favicon_path']))
                                <img src="{{ $settings['favicon_path'] }}" alt="Favicon" class="h-8 w-8 mb-2 rounded">
                            @endif
                            <input type="file" name="favicon_path" accept="image/png,image/x-icon,image/svg+xml"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-medium file:bg-teal/10 file:text-teal dark:file:text-teal-light
                                          hover:file:bg-teal/20 transition-colors cursor-pointer">
                            <p class="text-xs text-gray-400">PNG or ICO. 32×32px recommended.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Primary Brand Colour</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color"
                                       value="{{ $settings['primary_color'] ?? '#1697a9' }}"
                                       class="h-10 w-16 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                                <input type="text" name="primary_color_text"
                                       value="{{ $settings['primary_color'] ?? '#1697a9' }}"
                                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-surface-dark dark:text-gray-100 focus:border-teal focus:ring-teal/50 text-sm px-3 py-2"
                                       placeholder="#1697a9">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── EMAIL ─────────────────────────────────── --}}
                @if ($group === 'email')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">Email / SMTP Configuration</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-settings-field name="mail_mailer" label="Mail Driver" type="select" :value="$settings['mail_mailer'] ?? 'smtp'"
                            :options="['smtp' => 'SMTP', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'log' => 'Log (dev)']"/>
                        <x-settings-field name="mail_host" label="SMTP Host" placeholder="smtp.mailgun.org" :value="$settings['mail_host'] ?? ''"/>
                        <x-settings-field name="mail_port" label="SMTP Port" placeholder="587" :value="$settings['mail_port'] ?? '587'"/>
                        <x-settings-field name="mail_username" label="SMTP Username" :value="$settings['mail_username'] ?? ''"/>
                        <x-settings-field name="mail_password" label="SMTP Password" type="password" :value="$settings['mail_password'] ?? ''" hint="Leave blank to keep existing."/>
                        <x-settings-field name="mail_from_address" label="From Email" placeholder="hello@medswift.express" :value="$settings['mail_from_address'] ?? ''"/>
                        <x-settings-field name="mail_from_name" label="From Name" :value="$settings['mail_from_name'] ?? 'MedSwift Express'"/>
                    </div>

                    {{-- Test email panel --}}
                    <div class="mt-4 rounded-xl border border-dashed border-teal/40 bg-teal/5 dark:bg-teal/10 p-5"
                         x-data="{ email: '{{ auth()->user()?->email }}', sending: false, result: null }">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">🧪 Test Email Connection</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="email" x-model="email" placeholder="Send test to…"
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600
                                          dark:bg-surface-dark dark:text-gray-100
                                          focus:border-teal focus:ring-teal/50 text-sm px-3 py-2">
                            <button type="button" :disabled="sending"
                                    @click="sending=true; result=null;
                                        fetch('{{ route('admin.settings.test-email') }}', {
                                            method: 'POST',
                                            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                            body: JSON.stringify({to: email})
                                        })
                                        .then(r => r.json())
                                        .then(d => { result = d; sending = false; })
                                        .catch(() => { result = {ok: false, message: 'Request failed.'}; sending = false; })"
                                    class="rounded-lg bg-teal hover:bg-teal-dark disabled:opacity-60 px-5 py-2 text-sm font-semibold text-white
                                           transition-colors flex items-center gap-2 shrink-0">
                                <svg x-show="!sending" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                                </svg>
                                <svg x-show="sending" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                                <span x-text="sending ? 'Sending…' : 'Send Test Email'"></span>
                            </button>
                        </div>
                        <p x-show="result"
                           :class="result?.ok ? 'text-emerald-dark dark:text-emerald-light' : 'text-red-600 dark:text-red-400'"
                           class="mt-2 text-sm font-medium" x-text="result?.message"></p>
                    </div>
                @endif

                {{-- ── WHATSAPP ──────────────────────────────── --}}
                @if ($group === 'whatsapp')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">WhatsApp Business API</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Send automated tracking updates to clients via WhatsApp. Uses the <a href="https://developers.facebook.com/docs/whatsapp" target="_blank" class="text-teal hover:underline">Meta Cloud API</a>.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-settings-toggle name="whatsapp_enabled" label="Enable WhatsApp Notifications" :checked="($settings['whatsapp_enabled'] ?? '0') === '1'"/>
                        <x-settings-field name="whatsapp_api_url" label="API URL" :value="$settings['whatsapp_api_url'] ?? 'https://graph.facebook.com/v18.0'"/>
                        <x-settings-field name="whatsapp_phone_id" label="Phone Number ID" placeholder="Your WhatsApp phone number ID" :value="$settings['whatsapp_phone_id'] ?? ''"/>
                        <x-settings-field name="whatsapp_token" label="Access Token" type="password" :value="$settings['whatsapp_token'] ?? ''" hint="Leave blank to keep existing."/>
                        <x-settings-field name="whatsapp_verify_token" label="Webhook Verify Token" type="password" :value="$settings['whatsapp_verify_token'] ?? ''" hint="A secret string for webhook verification."/>
                    </div>
                @endif

                {{-- ── AI ────────────────────────────────────── --}}
                @if ($group === 'ai')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">AI & Swiftie Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure the Swiftie AI assistant. Uses <a href="https://console.groq.com" target="_blank" class="text-teal hover:underline">Groq</a> for ultra-fast LLM inference.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-settings-toggle name="ai_enabled" label="Enable Swiftie AI" :checked="($settings['ai_enabled'] ?? '1') === '1'"/>
                        <x-settings-field name="ai_provider" label="AI Provider" type="select" :value="$settings['ai_provider'] ?? 'groq'"
                            :options="['groq' => 'Groq (Recommended)', 'openai' => 'OpenAI']"/>
                        <x-settings-field name="groq_api_key" label="Groq API Key" type="password" :value="$settings['groq_api_key'] ?? ''"
                            hint="Get from console.groq.com. Leave blank to keep existing."/>
                        <x-settings-field name="groq_model" label="Groq Model" type="select" :value="$settings['groq_model'] ?? 'llama-3.3-70b-versatile'"
                            :options="[
                                'llama-3.3-70b-versatile' => 'Llama 3.3 70B (Recommended)',
                                'llama3-70b-8192' => 'Llama 3 70B',
                                'llama3-8b-8192' => 'Llama 3 8B (Faster)',
                                'mixtral-8x7b-32768' => 'Mixtral 8x7B (Long context)',
                            ]"/>
                    </div>
                @endif

                {{-- ── PAYMENTS ──────────────────────────────── --}}
                @if ($group === 'payments')
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 pb-2 border-b border-gray-100 dark:border-gray-700">Payment Gateway</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-settings-toggle name="payments_enabled" label="Enable Online Payments" :checked="($settings['payments_enabled'] ?? '0') === '1'"/>
                        <x-settings-field name="payment_gateway" label="Primary Gateway" type="select" :value="$settings['payment_gateway'] ?? 'paygate'"
                            :options="['paygate' => 'PayGate (South Africa)', 'stripe' => 'Stripe', 'yoco' => 'Yoco']"/>

                        <div class="sm:col-span-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3">PayGate</h4>
                        </div>
                        <x-settings-field name="paygate_id" label="PayGate Merchant ID" :value="$settings['paygate_id'] ?? ''"/>
                        <x-settings-field name="paygate_secret" label="PayGate Secret Key" type="password" :value="$settings['paygate_secret'] ?? ''" hint="Leave blank to keep existing."/>

                        <div class="sm:col-span-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3">Stripe</h4>
                        </div>
                        <x-settings-field name="stripe_public_key" label="Stripe Publishable Key" :value="$settings['stripe_public_key'] ?? ''"/>
                        <x-settings-field name="stripe_secret_key" label="Stripe Secret Key" type="password" :value="$settings['stripe_secret_key'] ?? ''" hint="Leave blank to keep existing."/>
                    </div>
                @endif

            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-surface-dark border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('admin.settings.group', $group) }}"
                   class="rounded-lg px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300
                          border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Reset
                </a>
                <button type="submit"
                        class="rounded-lg bg-teal hover:bg-teal-dark px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</x-sidebar-layout>
