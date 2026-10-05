<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class EnsureLoginUserIsVerified
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            auth()->guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            throw ValidationException::withMessages([
                'auth_notice_title' => ['Account verification required'],
                'auth_notice' => ['Your administrator account has not been verified. Please contact an administrator.'],
                Fortify::username() => ['Your administrator account has not been verified. Please contact an administrator.'],
            ]);
        }

        return $next($request);
    }
}
