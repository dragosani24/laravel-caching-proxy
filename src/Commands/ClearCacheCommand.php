<?php

namespace Vendor\LaravelCachingProxy\Commands;

use Illuminate\Console\Command;
use Vendor\LaravelCachingProxy\Cache\ResponseCache;

class ClearCacheCommand extends Command
{
    protected $name = 'caching-proxy:clear';
    protected $description = 'Clear the caching proxy cache';

    public function handle(ResponseCache $cache)
    {
        $cache->clear();
        $this->info('Cache cleared successfully');
        return 0;
    }
}
