<?php

return [
    'cache_path' => env('CACHING_PROXY_CACHE_PATH', storage_path('cache/proxy')),
    'default_ttl' => env('CACHING_PROXY_DEFAULT_TTL', 3600), // 1 hour
];

