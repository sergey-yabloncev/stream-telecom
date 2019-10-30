<?php

namespace Yabloncev\StreamTelecom;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return StreamTelecom::class;
    }
}
