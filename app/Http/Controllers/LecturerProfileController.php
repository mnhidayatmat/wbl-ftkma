<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LecturerProfileController extends Controller
{
    /**
     * Show lecturer profile
     */
    public function show()
    {
        $user = auth()->user();

        // Determine user's roles
        $roles = [];
        if ($user->isLecturer()) $roles[] = 'Lecturer';
        if ($user->isAt()) $roles[] = 'Academic Tutor';
        if ($user->isSupervisorLi()) $roles[] = 'LI Supervisor';
        if ($user->isIndustry()) $roles[] = 'Industry Coach';

        $roleDisplay = implode(' / ', $roles);

        return view('lecturers.profile.show', compact('user', 'roleDisplay'));
    }

    /**
     * Update lecturer profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        // Update user information
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('lecturer.profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
