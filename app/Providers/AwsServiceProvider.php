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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
