<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
}
