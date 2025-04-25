<?php

namespace Dragosani24\CachingProxy;

use Dragosani24\CachingProxy\Console\CachingProxyCommand;
use Illuminate\Support\ServiceProvider;

class CachingProxyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/caching-proxy.php', 'caching-proxy'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CachingProxyCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/caching-proxy.php' => config_path('caching-proxy.php'),
        ], 'config');
    }
}
