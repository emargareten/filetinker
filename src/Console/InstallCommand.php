<?php

namespace Emargareten\FileTinker\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filetinker:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all FileTinker resources';

    public function handle()
    {
        $this->comment('Publishing filetinker config...');
        $this->callSilent('vendor:publish', ['--tag' => 'filetinker-config']);

        $this->comment('Creating a tinker file...');
        $this->callSilent('vendor:publish', ['--tag' => 'filetinker-file']);

        $this->info('Done! You can now start to tinker from your file.');
        $this->info('Just write your code in the tinker file and run `php artisan filetinker:run`');

    }
}
