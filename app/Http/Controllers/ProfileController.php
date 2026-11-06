<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('Profile/Show', [
            'user' => $request->user()->only(['id', 'name', 'email']),
        ]);
    }

    // Add inside ProfileController:
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request
            ->user()
            ->forceFill([
                'password' => Hash::make($validated['password']),
            ])
            ->save();

        return back()->with('status', 'password-updated');
    }
}
