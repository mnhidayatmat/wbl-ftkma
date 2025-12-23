<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResendVerificationController extends Controller
{
    /**
     * Resend verification email for unauthenticated users.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['We could not find a user with that email address.'],
            ]);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('status', 'Your email address is already verified. You can now log in.');
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        return redirect()->route('login')
            ->with('status', 'Verification email sent! Please check your inbox and click the verification link.');
    }
}
