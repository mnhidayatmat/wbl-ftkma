<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Show the admin profile page.
     */
    public function show()
    {
        $user = Auth::user();

        return view('admin.profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin.profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the admin profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password updated successfully!');
    }
}
