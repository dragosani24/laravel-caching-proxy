<?php

namespace Dragosani24\CachingProxy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CachingProxyCommand extends Command
{
    protected $signature = 'caching-proxy 
                            {--port= : The port to run the proxy server on}
                            {--origin= : The origin server URL to proxy to}
                            {--clear-cache : Clear the cache}
                            {--config : Use config file settings}';

    protected $description = 'Run a caching proxy server or clear the cache';

    protected $serverProcess;

    public function handle()
    {
        if ($this->option('clear-cache')) {
            Cache::flush();
            $this->info('Cache cleared successfully!');
            return;
        }

        $port = $this->option('port');
        $origin = $this->option('origin');

        if ($this->option('config')) {
            $port = $port ?: config('caching-proxy.default_port');
            $origin = $origin ?: config('caching-proxy.default_origin');
        }

        if (!$port || !$origin) {
            $this->error('Both --port and --origin options are required or must be set in config');
            return;
        }

        $this->startProxyServer($port, $origin);
    }

    protected function startProxyServer($port, $origin)
    {
        $this->info("Starting caching proxy server on port {$port} forwarding to {$origin}");

        $routerPath = __DIR__.'/../../bin/router.php';
        $this->serverProcess = new Process([
            PHP_BINARY,
            '-S',
            "localhost:{$port}",
            $routerPath
        ]);

        $this->serverProcess->setEnv([
            'PROXY_ORIGIN' => $origin,
            'APP_BASE_PATH' => base_path(),
        ]);

        $this->serverProcess->start();
        $this->info("Server started. Press Ctrl+C to stop.");

        while ($this->serverProcess->isRunning()) {
            usleep(500000);
        }

        if ($this->serverProcess->isSuccessful()) {
            $this->info('Server stopped');
        } else {
            $this->error('Server stopped unexpectedly');
            $this->error($this->serverProcess->getErrorOutput());
        }
    }

    public function __destruct()
    {
        if ($this->serverProcess && $this->serverProcess->isRunning()) {
            $this->serverProcess->stop();
        }
    }
}
