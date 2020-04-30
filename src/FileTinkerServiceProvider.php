<?php

namespace Emargareten\FileTinker;

use Emargareten\FileTinker\Console\RunCommand;
use Illuminate\Support\ServiceProvider;
use Emargareten\FileTinker\Console\InstallCommand;

class FileTinkerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filetinker.php' => config_path('filetinker.php'),
            ], 'filetinker-config');


            $this->publishes([
                __DIR__ . '/../stubs/tinker.php' => config('filetinker.filepath'),
            ], 'filetinker-file');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filetinker.php', 'filetinker');

        $this->commands([
            InstallCommand::class,
            RunCommand::class,
        ]);
    }

}
