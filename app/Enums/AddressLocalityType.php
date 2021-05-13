<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class AddressLocalityType extends Enum
{
    const CITY = "city";
    const SUBURB = "suburb";
    const POST_TOWN = "post_town";
    const DISTRICT = "district";
}
