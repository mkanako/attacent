<?php

namespace Cc\Attacent\Console;

use Illuminate\Console\Command;

class AttacentLinkCommand extends Command
{
    protected $signature = 'attacent:link';
    protected $description = 'Create a symbolic link';

    public function handle()
    {
        $root = config('attacent.disk.root');
        if (empty($root)) {
            return $this->error('config `attacent.disk.root` is empty');
        }
        $dir = trim(basename($root), '/');
        if (file_exists(public_path($dir))) {
            return $this->error("The \"public/{$dir}\" directory already exists.");
        }
        $this->laravel->make('files')->link(
            $root,
            public_path($dir)
        );
        $this->line("The [public/{$dir}] directory has been linked.");
    }
}
