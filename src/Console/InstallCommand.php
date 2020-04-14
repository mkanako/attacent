<?php

namespace Cc\Attacent\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'attacent:install {--force}';
    protected $description = 'Install Command';

    public function handle()
    {
        $this->call('vendor:publish', ['--provider' => 'Cc\Attacent\AttacentServiceProvider', '--force' => $this->option('force')]);
        $this->call('migrate');
    }
}
