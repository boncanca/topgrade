<?php

namespace App\Providers;

use App\Services\Mail\BrevoMailService;
use App\Services\Mail\TransactionalMailService;
use App\Services\PaymentService;
use App\Services\Storage\CloudinaryAdapter;
use App\Services\StripePaymentService;
use Carbon\CarbonImmutable;
use Cloudinary\Cloudinary;
use CodebarAg\FlysystemCloudinary\CloudinaryDiskOptions;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentService::class,
            StripePaymentService::class
        );

        $this->app->bind(
            TransactionalMailService::class,
            BrevoMailService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Storage::extend('cloudinary', function (Application $app, array $config) {
            $diskOptions = CloudinaryDiskOptions::fromDiskAndConfig($config);
            $adapter = new CloudinaryAdapter(new Cloudinary($config), $diskOptions);

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
