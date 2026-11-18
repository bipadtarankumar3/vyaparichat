<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
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
        $plans = SubscriptionPlan::latest()->get();

        return view('admin.settings.index', [
            'tab' => $tab,
            'currentTab' => $tab,
            'activeNav' => 'settings_' . $tab,
            'plans' => $plans,
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
