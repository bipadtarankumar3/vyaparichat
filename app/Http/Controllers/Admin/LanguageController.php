<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    /**
     * Listing: Installed Languages.
     */
    public function index(Request $request): View
    {
        $languages = Language::all();

        return view('admin.languages.index', [
            'languages' => $languages,
            'activeNav' => 'languages',
        ]);
    }

    /**
     * Add: Store new Language.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:languages,code',
            'direction' => 'required|in:ltr,rtl',
        ]);

        Language::create([
            'name' => $validated['name'],
            'code' => strtolower($validated['code']),
            'direction' => $validated['direction'],
            'status' => 'active',
            'progress' => 100,
            'is_default' => false,
        ]);

        return back()->with('success', "Language \"{$validated['name']}\" added.");
    }

    /**
     * Action: Set Default Language.
     */
    public function setDefault(Language $language): RedirectResponse
    {
        Language::query()->update(['is_default' => false]);
        $language->update(['is_default' => true]);

        return back()->with('success', "{$language->name} set as primary system language.");
    }

    /**
     * Delete: Remove Language.
     */
    public function destroy(Language $language): RedirectResponse
    {
        if ($language->is_default) {
            return back()->with('error', "Cannot delete the default system language.");
        }

        $language->delete();
        return back()->with('success', "Language removed.");
    }
}
