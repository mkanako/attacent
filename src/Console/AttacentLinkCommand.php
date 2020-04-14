<?php

namespace Cc\Attacent\Console;

use Illuminate\Console\Command;

class AttacentLinkCommand extends Command
{
    protected $signature = 'attacent:link';

    protected $description = 'Create a symbolic link';

    public function handle()
    {
        $dir = trim(basename(config('attacent.disk.root')), '/');
        if (file_exists(public_path($dir))) {
            return $this->error("The \"public/{$dir}\" directory already exists.");
        }
        $this->laravel->make('files')->link(
            config('attacent.disk.root'),
            public_path($dir)
        );
        $this->info("The [public/{$dir}] directory has been linked.");
    }
}
