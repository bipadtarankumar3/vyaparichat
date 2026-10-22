<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmbeddedSignupController extends Controller
{
    /**
     * View: Meta Embedded Signup Configuration.
     */
    public function index(Request $request): View
    {
        return view('admin.embedded-signup.index', [
            'activeNav' => 'embedded',
        ]);
    }

    /**
     * Update: Save Embedded Signup settings.
     */
    public function save(Request $request): RedirectResponse
    {
        return back()->with('success', 'Meta Embedded Signup configuration saved successfully.');
    }
}
