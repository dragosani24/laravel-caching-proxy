<?php

require __DIR__.'/../../../../vendor/autoload.php';

$origin = getenv('PROXY_ORIGIN');
$path = $_SERVER['REQUEST_URI'];

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$proxy = $app->make('proxy.server');
$proxyResponse = $proxy->handleRequest($origin, $path);

http_response_code($proxyResponse['status']);

foreach ($proxyResponse['headers'] as $name => $values) {
    foreach ($values as $value) {
        header("{$name}: {$value}", false);
    }
}

echo $proxyResponse['body'];

$kernel->terminate($request, $response);
