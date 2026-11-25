<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'user' => [
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'organization_name' => $request->user()->organization_name,
                    'phone' => $request->user()->phone,
                    'role' => $request->user()->roleLabel(),
                    'created_at' => $request->user()->created_at?->toIso8601String(),
                    'email_verified' => (bool) $request->user()->email_verified_at,
                ],
                'status_message' => session('status'),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'old' => [
                    'name' => old('name'),
                    'email' => old('email'),
                    'organization_name' => old('organization_name'),
                    'phone' => old('phone'),
                ],
            ]);
        }

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
