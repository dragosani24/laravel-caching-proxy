<?php

namespace Vendor\LaravelCachingProxy\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StartProxyCommand extends Command
{
    protected $name = 'caching-proxy';
    protected $description = 'Start the caching proxy server';

    public function handle()
    {
        $port = $this->option('port');
        $origin = $this->option('origin');

        if (!$port || !$origin) {
            $this->error('Both --port and --origin options are required');
            return 1;
        }

        $this->info("Starting caching proxy server on port {$port} for origin {$origin}");

        $command = [
            PHP_BINARY,
            '-S',
            "{$this->option('host')}:{$this->option('port')}",
            '-t',
            __DIR__ . '/../../public',
        ];

        putenv("PROXY_ORIGIN={$origin}");

        $process = new Process($command);
        $process->setTimeout(null);
        $process->start();

        foreach ($process as $type => $data) {
            $this->output->write($data);
        }

        return 0;
    }

    protected function getOptions()
    {
        return [
            ['port', null, InputOption::VALUE_REQUIRED, 'Port to run the proxy server on'],
            ['origin', null, InputOption::VALUE_REQUIRED, 'Origin server URL to proxy'],
        ];
    }
}