<?php

namespace Vendor\LaravelCachingProxy\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Vendor\LaravelCachingProxy\Cache\ResponseCache;

class ProxyServer
{
    protected $cache;
    protected $httpClient;

    public function __construct(ResponseCache $cache, Client $httpClient)
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    public function handleRequest(string $origin, string $path): array
    {
        $cacheKey = $origin . $path;
        $cachedResponse = $this->cache->get($cacheKey);

        if ($cachedResponse) {
            $cachedResponse['headers']['X-Cache'] = 'HIT';
            return $cachedResponse;
        }

        try {
            $response = $this->httpClient->get(rtrim($origin, '/') . '/' . ltrim($path, '/'));

            $responseData = [
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => (string) $response->getBody(),
            ];

            $responseData['headers']['X-Cache'] = 'MISS';

            $this->cache->put($cacheKey, $responseData);

            return $responseData;
        } catch (GuzzleException $e) {
            return [
                'status' => 500,
                'headers' => [],
                'body' => 'Proxy error: ' . $e->getMessage(),
            ];
        }
    }
}
