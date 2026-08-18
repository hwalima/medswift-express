<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['group' => 'general', 'key' => 'app_name',    'value' => 'MedSwift Express', 'type' => 'text'],
            ['group' => 'general', 'key' => 'app_tagline',  'value' => 'Medical Courier & Logistics', 'type' => 'text'],
            ['group' => 'general', 'key' => 'timezone',     'value' => 'Africa/Johannesburg', 'type' => 'text'],
            ['group' => 'general', 'key' => 'currency',     'value' => 'ZAR', 'type' => 'text'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => 'R', 'type' => 'text'],
            ['group' => 'general', 'key' => 'country',      'value' => 'ZA', 'type' => 'text'],

            // Branding
            ['group' => 'branding', 'key' => 'logo_path',    'value' => '/images/logo.png', 'type' => 'file'],
            ['group' => 'branding', 'key' => 'favicon_path', 'value' => '/favicon.png', 'type' => 'file'],
            ['group' => 'branding', 'key' => 'primary_color', 'value' => '#1697a9', 'type' => 'text'],

            // Email / SMTP
            ['group' => 'email', 'key' => 'mail_mailer',      'value' => 'smtp', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_host',        'value' => 'smtp.mailgun.org', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_port',        'value' => '587', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_username',    'value' => '', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_password',    'value' => '', 'type' => 'password'],
            ['group' => 'email', 'key' => 'mail_from_address','value' => 'hello@medswift.express', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_from_name',   'value' => 'MedSwift Express', 'type' => 'text'],

            // WhatsApp
            ['group' => 'whatsapp', 'key' => 'whatsapp_enabled',     'value' => '0', 'type' => 'boolean'],
            ['group' => 'whatsapp', 'key' => 'whatsapp_api_url',     'value' => 'https://graph.facebook.com/v18.0', 'type' => 'text'],
            ['group' => 'whatsapp', 'key' => 'whatsapp_token',       'value' => '', 'type' => 'password'],
            ['group' => 'whatsapp', 'key' => 'whatsapp_phone_id',    'value' => '', 'type' => 'text'],
            ['group' => 'whatsapp', 'key' => 'whatsapp_verify_token','value' => '', 'type' => 'password'],

            // AI
            ['group' => 'ai', 'key' => 'ai_provider',    'value' => 'groq', 'type' => 'text'],
            ['group' => 'ai', 'key' => 'groq_api_key',   'value' => '', 'type' => 'password'],
            ['group' => 'ai', 'key' => 'groq_model',     'value' => 'llama-3.3-70b-versatile', 'type' => 'text'],
            ['group' => 'ai', 'key' => 'ai_enabled',     'value' => '1', 'type' => 'boolean'],

            // Payments
            ['group' => 'payments', 'key' => 'payment_gateway',   'value' => 'paygate', 'type' => 'text'],
            ['group' => 'payments', 'key' => 'paygate_id',        'value' => '', 'type' => 'text'],
            ['group' => 'payments', 'key' => 'paygate_secret',    'value' => '', 'type' => 'password'],
            ['group' => 'payments', 'key' => 'stripe_public_key', 'value' => '', 'type' => 'text'],
            ['group' => 'payments', 'key' => 'stripe_secret_key', 'value' => '', 'type' => 'password'],
            ['group' => 'payments', 'key' => 'payments_enabled',  'value' => '0', 'type' => 'boolean'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('Default settings seeded.');
    }
}
