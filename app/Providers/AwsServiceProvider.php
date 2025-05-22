<?php

namespace App\Providers;

use Aws\Ec2\Ec2Client;
use Illuminate\Support\ServiceProvider;

class AwsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
