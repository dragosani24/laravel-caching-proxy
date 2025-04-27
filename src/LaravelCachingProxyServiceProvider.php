<?php

namespace Vendor\LaravelCachingProxy;

use Illuminate\Support\ServiceProvider;
use Vendor\LaravelCachingProxy\Commands\ClearCacheCommand;
use Vendor\LaravelCachingProxy\Commands\StartProxyCommand;

class LaravelCachingProxyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/caching-proxy.php', 'caching-proxy'
        );

        $this->app->singleton('response.cache', function ($app) {
            return new Cache\ResponseCache(
                $app['config']->get('caching-proxy.cache_path') ?? storage_path('cache/proxy')
            );
        });

        $this->app->bind('proxy.server', function ($app) {
            return new Proxy\ProxyServer(
                $app->make('response.cache'),
                $app->make(\GuzzleHttp\Client::class)
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/caching-proxy.php' => config_path('caching-proxy.php'),
            ], 'caching-proxy-config');

            $this->commands([
                StartProxyCommand::class,
                ClearCacheCommand::class,
            ]);
        }
    }
}