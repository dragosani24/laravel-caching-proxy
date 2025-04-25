<?php

require __DIR__.'/../../vendor/autoload.php';

$origin = getenv('PROXY_ORIGIN');
$app = require_once getenv('APP_BASE_PATH').'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Handle the request
$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$query = $_SERVER['QUERY_STRING'] ?? '';
$headers = getallheaders();
$body = file_get_contents('php://input');

// Create a cache key
$cacheKey = md5("{$method}:{$path}:{$query}:".json_encode($headers).":{$body}");

// Try to get cached response
if ($cached = Illuminate\Support\Facades\Cache::get($cacheKey)) {
    $response = $cached['response'];
    $headers = $cached['headers'];
    $headers['X-Cache'] = 'HIT';

    foreach ($headers as $name => $value) {
        header("{$name}: {$value}");
    }

    echo $response;
    exit;
}

// No cache hit, forward to origin server
$client = new GuzzleHttp\Client();

try {
    $originResponse = $client->request($method, $origin.$path, [
        'headers' => $headers,
        'query' => $query,
        'body' => $body,
        'http_errors' => false,
    ]);

    $response = (string) $originResponse->getBody();
    $status = $originResponse->getStatusCode();
    $originHeaders = $originResponse->getHeaders();
    $originHeaders['X-Cache'] = ['MISS'];

    Illuminate\Support\Facades\Cache::put($cacheKey, [
        'response' => $response,
        'headers' => $originHeaders,
    ], now()->addHours(1));

    http_response_code($status);

    foreach ($originHeaders as $name => $values) {
        foreach ($values as $value) {
            header("{$name}: {$value}", false);
        }
    }

    echo $response;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Proxy error: ' . $e->getMessage()]);
}

$kernel->terminate($request, $response);
