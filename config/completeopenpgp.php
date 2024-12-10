<?php

return [
    'key_size' => 2048,
    'key_algorithm' => 'RSA',
    'default_passphrase' => env('PGP_DEFAULT_PASSPHRASE', 'your-passphrase-here'),
    'gnupg_home' => env('GNUPG_HOME', '/path/to/gnupg_home'),
];

