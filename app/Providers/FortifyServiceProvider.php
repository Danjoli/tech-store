<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::updateUserProfileInformationUsing(
            UpdateUserProfileInformation::class
        );

        Fortify::updateUserPasswordsUsing(
            UpdateUserPassword::class
        );

        Fortify::resetUserPasswordsUsing(
            ResetUserPassword::class
        );

        $this->configureViews();
        $this->configureAuthentication();
        $this->configureRateLimiting();
    }

    private function configureViews(): void
    {
        Fortify::loginView(
            fn () => Inertia::render('Auth/Login')
        );

        Fortify::registerView(
            fn () => Inertia::render('Auth/Register')
        );

        Fortify::requestPasswordResetLinkView(
            fn () => Inertia::render('Auth/ForgotPassword')
        );

        Fortify::resetPasswordView(
            fn (Request $request) => Inertia::render(
                'Auth/ResetPassword',
                [
                    'email' => $request->string('email')->toString(),
                    'token' => $request->route('token'),
                ]
            )
        );

        Fortify::verifyEmailView(
            fn () => Inertia::render('Auth/VerifyEmail')
        );

        Fortify::confirmPasswordView(
            fn () => Inertia::render('Auth/ConfirmPassword')
        );
    }

    private function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function (Request $request): ?User {
            $user = User::where(
                'email',
                Str::lower((string) $request->input('email'))
            )->first();

            if (
                $user
                && $user->is_active
                && Hash::check(
                    (string) $request->input('password'),
                    $user->password
                )
            ) {
                return $user;
            }

            return null;
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(
            'login',
            function (Request $request): Limit {
                $email = Str::transliterate(
                    Str::lower(
                        (string) $request->input(
                            Fortify::username()
                        )
                    )
                );

                return Limit::perMinute(5)->by(
                    $email.'|'.$request->ip()
                );
            }
        );

        RateLimiter::for(
            'two-factor',
            fn (Request $request): Limit => Limit::perMinute(5)
                ->by((string) $request->session()->get('login.id'))
        );
    }
}
