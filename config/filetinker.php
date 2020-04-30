<?php

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
