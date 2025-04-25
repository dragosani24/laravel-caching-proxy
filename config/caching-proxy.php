<?php

return [
    'default_port' => env('CACHING_PROXY_PORT', 3000),
    'default_origin' => env('CACHING_PROXY_ORIGIN', 'http://localhost'),
    'cache_ttl' => env('CACHING_PROXY_TTL', 60), // in minutes
];
