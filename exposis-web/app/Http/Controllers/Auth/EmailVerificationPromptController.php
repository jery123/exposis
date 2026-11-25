<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View|JsonResponse
    {
        if ($request->expectsJson()) {
            if ($request->user()->hasVerifiedEmail()) {
                return response()->json(['redirectTo' => route('dashboard', absolute: false)]);
            }

            return response()->json([
                'status_message' => session('status'),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
            ]);
        }

        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }
}
