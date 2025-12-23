<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Verify the hash matches
        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Your email has already been verified. You can now log in.');
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        // Refresh the user model to ensure the change is reflected
        $user->refresh();

        // If user is logged in, redirect to dashboard, otherwise to login
        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->intended(route('dashboard').'?verified=1');
        }

        return redirect()->route('login')->with('status', 'Email verified successfully! You can now log in.');
    }
}
