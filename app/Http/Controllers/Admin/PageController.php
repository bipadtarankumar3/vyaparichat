<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Listing: CMS Pages.
     */
    public function index(Request $request): View
    {
        $pages = Page::latest()->get();

        return view('admin.pages.index', [
            'pages' => $pages,
            'activeNav' => 'pages',
        ]);
    }

    /**
     * Add: Store new CMS Page.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:pages,slug',
            'type' => 'required|string|max:50',
            'content' => 'nullable|string',
            'status' => 'required|in:published,draft',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        Page::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
        ]);

        return back()->with('success', "CMS Page \"{$validated['title']}\" created.");
    }

    /**
     * Update: Update CMS Page.
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'content' => 'nullable|string',
            'status' => 'required|in:published,draft',
        ]);

        $page->update($validated);

        return back()->with('success', "CMS Page \"{$page->title}\" updated.");
    }

    /**
     * Delete: Remove CMS Page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();
        return back()->with('success', "CMS Page deleted.");
    }
}
