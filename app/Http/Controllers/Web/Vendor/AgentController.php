<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentController extends BaseVendorController
{
    /**
     * Listing: Team Members / Agents.
     */
    public function index(Request $request): View
    {
        $vendor = $this->getActiveVendor($request);
        $agents = $vendor->users()->get();

        return view('vendor.agents.index', [
            'vendor' => $vendor,
            'agents' => $agents,
            'activeNav' => 'agents',
        ]);
    }

    /**
     * Add: Store new Agent / Team Member.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,agent,manager',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'is_super_admin' => false,
            ]
        );

        $role = $validated['role'] === 'manager' ? 'admin' : $validated['role'];

        $vendor->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $role,
                'permissions' => json_encode(['messaging', 'contacts', 'inbox']),
                'status' => 'active',
            ],
        ]);

        return back()->with('success', "Agent \"{$validated['name']}\" added successfully.");
    }

    /**
     * Update: Update Agent role.
     */
    public function update(Request $request, User $agent): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        $role = $request->input('role', 'agent');
        if ($role === 'manager') {
            $role = 'admin';
        }

        $vendor->users()->updateExistingPivot($agent->id, [
            'role' => $role,
        ]);

        return back()->with('success', "Agent role updated to {$role}.");
    }

    /**
     * Delete: Remove Agent from team.
     */
    public function destroy(Request $request, User $agent): RedirectResponse
    {
        $vendor = $this->getActiveVendor($request);
        $vendor->users()->detach($agent->id);

        return back()->with('success', "Agent \"{$agent->name}\" removed from team.");
    }
}
