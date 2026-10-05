<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\EnsureLoginUserIsAdmin;
use App\Actions\Fortify\EnsureLoginUserIsVerified;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Contracts\RedirectsIfTwoFactorAuthenticatable;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureAuthentication();
        $this->configureLoginPipeline();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn (Request $request) => Inertia::render('auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'status' => $request->session()->get('status'),
        ]));

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]));

        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::registerView(fn () => Inertia::render('auth/Register', [
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]));

        Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/TwoFactorChallenge'));

        Fortify::confirmPasswordView(fn () => Inertia::render('auth/ConfirmPassword'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('passkeys', function (Request $request) {
            return Limit::perMinute(10)->by(
                ($request->input('credential.id') ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }

    /**
     * Configure custom credential authentication with specific field error feedback.
     */
    private function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $username = Fortify::username();
            $login = (string) $request->input($username);
            $password = (string) $request->input('password');

            $user = User::where($username, $login)
                ->orWhereRaw('LOWER('.$username.') = ?', [Str::lower($login)])
                ->first();

            if (! $user) {
                app(LoginRateLimiter::class)->increment($request);

                event(new Failed(config('fortify.guard'), null, [
                    $username => $login,
                    'password' => $password,
                ]));

                throw ValidationException::withMessages([
                    'auth_notice_title' => ['Account not found'],
                    'auth_notice' => ['No administrator account was found with this email address.'],
                    $username => ['No account was found with this email address.'],
                ]);
            }

            if (! Hash::check($password, $user->password)) {
                app(LoginRateLimiter::class)->increment($request);

                event(new Failed(config('fortify.guard'), $user, [
                    $username => $login,
                    'password' => $password,
                ]));

                throw ValidationException::withMessages([
                    'auth_notice_title' => ['Incorrect password'],
                    'auth_notice' => ['The password you entered is incorrect. Please check your password and try again.'],
                    'password' => ['The password you entered is incorrect.'],
                ]);
            }

            if (config('hashing.rehash_on_login', true) && Hash::needsRehash($user->password)) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }

            return $user;
        });
    }

    /**
     * Configure Fortify login authentication pipeline.
     */
    private function configureLoginPipeline(): void
    {
        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectsIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                EnsureLoginUserIsVerified::class,
                EnsureLoginUserIsAdmin::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}
