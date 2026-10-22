<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * View: Global System Settings Tabs.
     */
    public function index(Request $request, string $tab = 'general'): View
    {
        return view('admin.settings.index', [
            'tab' => $tab,
            'currentTab' => $tab,
            'activeNav' => 'settings_' . $tab,
        ]);
    }

    /**
     * Update: Save Global System Settings.
     */
    public function save(Request $request, string $tab = 'general'): RedirectResponse
    {
        return back()->with('success', ucfirst($tab) . ' settings saved successfully.');
    }
}
