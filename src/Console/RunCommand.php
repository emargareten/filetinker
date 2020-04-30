<?php

namespace Emargareten\FileTinker\Console;

use Emargareten\FileTinker\ExecutionClosure;
use Illuminate\Console\Command;
use Laravel\Tinker\ClassAliasAutoloader;
use Psy\Configuration;
use Psy\Shell;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Output\BufferedOutput;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filetinker:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs tinker from file code';

    /** @var \Symfony\Component\Console\Output\BufferedOutput */
    protected $bufferedOutput;

    /** @var \Psy\Shell */
    protected $shell;


    public function __construct()
    {
        parent::__construct();

        $this->bufferedOutput = new BufferedOutput();

        $this->shell = $this->createShell($this->bufferedOutput);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $code = file_get_contents($this->getTinkerFile());
        $code = $this->removeCommentsAndTags($code);

        $this->shell->addInput($code);

        $closure = new ExecutionClosure($this->shell);

        $closure->execute();

        $output = $this->cleanOutput($this->bufferedOutput->fetch());

        $this->writeOutput($output);
    }

    protected function createShell(BufferedOutput $output): Shell
    {
        $config = new Configuration([
            'prompt'      => "\n",
            'updateCheck' => 'never',
            'configFile'  => config('filetinker.config_file') !== null ? base_path() . '/' . config('filetinker.config_file') : null,
        ]);

        $config->getPresenter()->addCasters([
            'Illuminate\Support\Collection'      => 'Laravel\Tinker\TinkerCaster::castCollection',
            'Illuminate\Database\Eloquent\Model' => 'Laravel\Tinker\TinkerCaster::castModel',
            'Illuminate\Foundation\Application'  => 'Laravel\Tinker\TinkerCaster::castApplication',
        ]);

        $shell = new Shell($config);

        $shell->setOutput($output);

        $path = base_path('vendor/composer/autoload_classmap.php');

        if (file_exists($path)) {
            ClassAliasAutoloader::register($shell, $path);
        }

        return $shell;
    }


    protected function getTinkerFile()
    {
        $file = config('filetinker.filepath');

        if (!file_exists($file)) {
            throw new LogicException("File ${file} does not exist please run `filetinker:install` and/or update filepath specified in the config file");
        }

        return $file;
    }

    public function removeCommentsAndTags(string $code): string
    {
        $tokens = collect(token_get_all($code));

        return $tokens->reduce(function ($carry, $token) {
            if (is_string($token)) {
                return $carry . $token;
            }

            [$id, $text] = $token;

            if ($id === T_COMMENT || $id === T_DOC_COMMENT || $id === T_OPEN_TAG || $id === T_CLOSE_TAG) {
                $text = '';
            }

            return $carry . $text;
        }, '');
    }

    protected function cleanOutput(string $output): string
    {
        $output = preg_replace('/(?s)(<aside.*?<\/aside>)|Exit:  Ctrl\+D/ms', '$2', $output);

        return trim($output);
    }

    protected function writeOutput($output)
    {
        if (config('filetinker.date_prepend_format')) {
            $this->info("[" . date(config('filetinker.date_prepend_format')) . "]");
        }

        echo $output . "\n";
    }
}
