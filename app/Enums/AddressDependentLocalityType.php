<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class AddressDependentLocalityType extends Enum
{
    const NEIGHBORHOOD = "neighborhood";
    const DISTRICT = "district";
    const TOWNLAND = "townland";
    const VILLAGE_TOWNSHIP = "village_township";
    const SUBURB = "suburb";
}
