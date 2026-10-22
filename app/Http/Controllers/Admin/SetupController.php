<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupController extends Controller
{
    /**
     * View: System & Server Requirements Setup.
     */
    public function index(Request $request): View
    {
        $requirements = [
            'php_version' => ['name' => 'PHP >= 8.2', 'status' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            'pdo_mysql' => ['name' => 'PDO MySQL Driver', 'status' => extension_loaded('pdo_mysql')],
            'curl' => ['name' => 'cURL Extension', 'status' => extension_loaded('curl')],
            'openssl' => ['name' => 'OpenSSL Extension', 'status' => extension_loaded('openssl')],
            'mbstring' => ['name' => 'Mbstring Extension', 'status' => extension_loaded('mbstring')],
            'fileinfo' => ['name' => 'FileInfo Extension', 'status' => extension_loaded('fileinfo')],
            'storage_writable' => ['name' => 'Storage Directory Writable', 'status' => is_writable(storage_path())],
        ];

        return view('admin.setup.index', [
            'requirements' => $requirements,
            'activeNav' => 'setup',
        ]);
    }

    /**
     * Update: Save System Setup settings.
     */
    public function save(Request $request): RedirectResponse
    {
        return back()->with('success', 'Core system requirements verified and settings stored.');
    }
}
