<?php

namespace App\Providers;

use Aws\CloudWatch\CloudWatchClient;
use Aws\Ec2\Ec2Client;
use Aws\Ssm\SsmClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Ec2Client::class, function ($app) {
            return new Ec2Client([
                'region' => config("aws.region"),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('aws.key'),
                    'secret' => config('aws.secret'),
                ],
            ]);
        });

        $this->app->singleton(SsmClient::class, function ($app) {
            return new SsmClient([
                'region' => config('aws.region'),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('aws.key'),
                    'secret' => config('aws.secret'),
                ],
            ]);
        });

        $this->app->singleton(CloudWatchClient::class, function ($app) {
            return new CloudWatchClient([
                'region' => config('aws.region'),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('aws.key'),
                    'secret' => config('aws.secret'),
                ],
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        Gate::define('viewPulse', function (\App\Models\User $user) {
            return $user->isAdmin();
        });
    }
}
