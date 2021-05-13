<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class AddressPostalCodeType extends Enum
{
    const POSTAL = "postal";
    const ZIP = "zip";
    const EIRCODE = "eircode";
    const PIN = "pin";
}
