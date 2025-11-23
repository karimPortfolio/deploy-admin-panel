<?php

namespace App\Providers;

use Aws\CloudWatch\CloudWatchClient;
use Aws\Ec2\Ec2Client;
use Aws\Rds\RdsClient;
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
            $options = [
                'region' => config('aws.region', 'us-east-1'),
                'version' => 'latest',
            ];
    
            if ($key = config('aws.key')) {
                $secret = config('aws.secret');
                if ($secret) {
                    $options['credentials'] = [
                        'key' => $key,
                        'secret' => $secret,
                    ];
                }
            }
    
            return new Ec2Client($options);
        });

        $this->app->singleton(SsmClient::class, function ($app) {
            $options = [
                'region' => config('aws.region', 'us-east-1'),
                'version' => 'latest',
            ];
            if ($key = config('aws.key') && $secret = config('aws.secret')) {
                $options['credentials'] = ['key' => config('aws.key'), 'secret' => config('aws.secret')];
            }
            return new SsmClient($options);
        });

        $this->app->singleton(CloudWatchClient::class, function ($app) {
            $options = [
                'region' => config('aws.region', 'us-east-1'),
                'version' => 'latest',
            ];
            if (config('aws.key') && config('aws.secret')) {
                $options['credentials'] = ['key' => config('aws.key'), 'secret' => config('aws.secret')];
            }
            return new CloudWatchClient($options);
        });

        $this->app->singleton(RdsClient::class, function ($app) {
            $options = [
                'region' => config('aws.region', 'us-east-1'),
                'version' => 'latest',
            ];
            if (config('aws.key') && config('aws.secret')) {
                $options['credentials'] = ['key' => config('aws.key'), 'secret' => config('aws.secret')];
            }
            return new RdsClient($options);
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
