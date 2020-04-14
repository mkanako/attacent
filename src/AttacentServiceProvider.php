<?php

namespace Cc\Attacent;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AttacentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/config.php' => config_path('attacent.php')], 'Attacent-config');
            $this->publishes([__DIR__ . '/Database/migrations' => database_path('migrations')], 'Attacent-migrations');
        }
    }

    public function register()
    {
        config(
            Arr::dot(
                ['attacent' => config('attacent.disk')],
                'filesystems.disks.'
            )
        );
        $this->commands([
            Console\InstallCommand::class,
            Console\AttacentLinkCommand::class,
        ]);
    }
}
