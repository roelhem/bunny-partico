<?php

namespace App\GraphQL\Queries;

class AddressFormats
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return \StaticData::getAddressFormats();
    }
}
