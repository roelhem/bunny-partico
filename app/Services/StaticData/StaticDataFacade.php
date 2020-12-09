<?php


namespace App\Services\StaticData;


use Illuminate\Support\Facades\Facade;

class StaticDataFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StaticData::class;
    }
}
