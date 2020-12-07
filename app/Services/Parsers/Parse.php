<?php

namespace App\Services\Parsers;


use Illuminate\Support\Facades\Facade;

class Parse extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ParseService::class;
    }

}
