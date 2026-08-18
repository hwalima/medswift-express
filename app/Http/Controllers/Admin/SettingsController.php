<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private array $groups = ['general', 'branding', 'email', 'whatsapp', 'ai', 'payments'];

    public function index(): View
    {
        return $this->show('general');
    }

    public function show(string $group): View
    {
        abort_unless(in_array($group, $this->groups), 404);

        $settings = Setting::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.index', compact('group', 'settings'));
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        abort_unless(in_array($group, $this->groups), 404);

        $data    = $request->except(['_token', '_method']);
        $types   = Setting::where('group', $group)->pluck('type', 'key')->toArray();

        // Handle file uploads
        foreach (['logo_path', 'favicon_path'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('uploads', 'public');
                $data[$field] = '/storage/' . $path;
            } elseif (isset($data[$field]) && empty($data[$field])) {
                unset($data[$field]); // keep existing value if field empty
            }
        }

        // Skip empty password fields (keep existing value)
        foreach ($types as $key => $type) {
            if ($type === 'password' && isset($data[$key]) && $data[$key] === '') {
                unset($data[$key]);
            }
        }

        // Boolean checkboxes (unchecked = not submitted = 0)
        foreach ($types as $key => $type) {
            if ($type === 'boolean') {
                $data[$key] = isset($data[$key]) ? '1' : '0';
            }
        }

        Setting::saveGroup($group, $data, $types);

        return back()->with('success', ucfirst($group) . ' settings saved.');
    }

    public function testEmail(Request $request): JsonResponse
    {
        $request->validate(['to' => 'required|email']);

        // Apply saved SMTP settings at runtime without touching .env
        Config::set('mail.default', Setting::get('mail_mailer', 'smtp'));
        Config::set('mail.mailers.smtp.host', Setting::get('mail_host', config('mail.mailers.smtp.host')));
        Config::set('mail.mailers.smtp.port', (int) Setting::get('mail_port', 587));
        Config::set('mail.mailers.smtp.username', Setting::get('mail_username'));
        Config::set('mail.mailers.smtp.password', Setting::get('mail_password'));
        Config::set('mail.from.address', Setting::get('mail_from_address', 'hello@medswift.express'));
        Config::set('mail.from.name', Setting::get('mail_from_name', 'MedSwift Express'));

        try {
            Mail::raw(
                "✅ Test email from MedSwift Express!\n\nYour SMTP configuration is working correctly.\n\nSent: " . now()->toDateTimeString(),
                fn (\Illuminate\Mail\Message $msg) => $msg
                    ->to($request->input('to'))
                    ->subject('MedSwift Express — SMTP Test ✓')
            );

            return response()->json(['ok' => true, 'message' => '✓ Test email sent to ' . $request->input('to')]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => '✗ ' . $e->getMessage()], 422);
        }
    }
}
