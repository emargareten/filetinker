# Artisan Tinker in your IDE

Artisan's tinker command is a great way to tinker with your application in the terminal. Unfortunately running a few lines of code, making edits, and copy/pasting code can be bothersome. Wouldn't it be great to tinker from a file right in your IDE with code completion and all other features?

This package will add a file in your project from where you will be able to run your tinker code.

*This package is still under development*

## Installation

You can install the package via composer:

```bash
composer require emargareten/filetinker --dev
```

Next, you must publish the files from this package by running this command.

```bash
php artisan filetinker:install
```

This will publish the config file to your config directory and create a new file tinker.php in your root directory which you will use to write the code.

You will probably want to add tinker.php to your .gitignore file.

This is the content that will be published to `config/filetinker.php`

```php
return [

    /*
     * The file that tinker will run from.
     */
    'filepath' => base_path('tinker.php'),

    /*
     * Prepends the output with date in this format.
     * To remove prepended date set this value to null
     */
    'date_prepend_format' => 'Y-m-d H:i:s',

    /*
     * If you want to fine-tune PsySH configuration specify
     * configuration file name, relative to the root of your
     * application directory.
     */
    'config_file' => env('PSYSH_CONFIG', null),
];
```

## Usage

Write your code in tinker.php (or whichever file is configured to run filetinker) then run `php artisan filetinker` and see the output in your terminal!

(You can set up run configuration and/or keyboard shortcuts in your IDE to run this command.)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
