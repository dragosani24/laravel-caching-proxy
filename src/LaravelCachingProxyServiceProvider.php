<?php

namespace Vendor\LaravelCachingProxy;

use Illuminate\Support\ServiceProvider;
use Vendor\LaravelCachingProxy\Commands\ClearCacheCommand;
use Vendor\LaravelCachingProxy\Commands\StartProxyCommand;

class LaravelCachingProxyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('response.cache', function ($app) {
            return new Cache\ResponseCache(storage_path('cache/proxy'));
        });

        $this->app->bind('proxy.server', function ($app) {
            return new Proxy\ProxyServer(
                $app->make('response.cache'),
                $app->make(\GuzzleHttp\Client::class)
            );
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                StartProxyCommand::class,
                ClearCacheCommand::class,
            ]);
        }
    }
}
