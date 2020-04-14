<?php

namespace Cc\Attacent;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AttacentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config(
            Arr::dot(
                ['attacent' => config('attacent.disk')],
                'filesystems.disks.'
            )
        );
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/config.php' => config_path('attacent.php')]);
            $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
            $this->commands([
                Console\InstallCommand::class,
                Console\AttacentLinkCommand::class,
            ]);
        }
    }

    public function register()
    {
    }
}
