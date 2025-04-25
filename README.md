# Laravel Caching Proxy

A caching proxy server for Laravel applications.

## Installation

```bash
composer require dragosani24/laravel-caching-proxy
```

## Usage

### Start the proxy server
```bash
php artisan caching-proxy --port 3000 --origin http://example.com
```

### Use configuration values
```bash
php artisan caching-proxy --config
```

### Clear cache
```bash
php artisan caching-proxy --clear-cache
```

### Standalone executable
```bash
./vendor/bin/caching-proxy --port 3000 --origin http://example.com
```

## Configuration

Publish the configuration file:
```bash
php artisan vendor:publish --provider="dragosani24\\CachingProxy\\CachingProxyServiceProvider" --tag="config"
```

Then edit `config/caching-proxy.php`:
```php
return [
    'default_port' => env('CACHING_PROXY_PORT', 3000),
    'default_origin' => env('CACHING_PROXY_ORIGIN', 'http://localhost'),
    'cache_ttl' => env('CACHING_PROXY_TTL', 60), // in minutes
];
```