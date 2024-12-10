<?php

namespace CompleteOpenPGP\Facades;

use Illuminate\Support\Facades\Facade;

class CompleteOpenPGP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'completeopenpgp';
    }
}

