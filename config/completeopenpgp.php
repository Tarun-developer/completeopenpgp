<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cryptographic Options
    |--------------------------------------------------------------------------
    |
    | Define the default key directories and environment variables for
    | CompleteOpenPGP keys, passphrases, and curves.
    |
    */

    'key_storage' => storage_path('keys'),

    'default_curve' => 'nistp256',

    'signing_key' => env('PGP_SIGNING_KEY'),

    'encryption_key' => env('PGP_ENCRYPTION_KEY'),

    'passphrase' => env('PGP_PASSPHRASE'),
];
