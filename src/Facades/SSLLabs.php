<?php

namespace SSLLabs\Laravel\Facades;
use Illuminate\Support\Facades\Facade;

class SSLLabs extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'ssllabs';
    }
}
