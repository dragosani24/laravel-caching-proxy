<?php

namespace Vendor\LaravelCachingProxy\Cache;

use Illuminate\Support\Facades\File;

class ResponseCache
{
    protected $cachePath;

    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;

        if (!File::exists($this->cachePath)) {
            File::makeDirectory($this->cachePath, 0755, true);
        }
    }

    public function get(string $key): ?array
    {
        $path = $this->getCacheFilePath($key);

        if (File::exists($path)) {
            return json_decode(File::get($path), true);
        }

        return null;
    }

    public function put(string $key, array $response): void
    {
        $path = $this->getCacheFilePath($key);
        File::put($path, json_encode($response));
    }

    public function clear(): void
    {
        File::cleanDirectory($this->cachePath);
    }

    protected function getCacheFilePath(string $key): string
    {
        return $this->cachePath . '/' . md5($key) . '.json';
    }
}