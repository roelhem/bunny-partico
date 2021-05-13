<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class AddressAdministrativeAreaType extends Enum
{
    const EMIRATE = "emirate";
    const PROVINCE = "province";
    const STATE = "state";
    const PARISH = "parish";
    const ISLAND = "island";
    const DEPARTMENT = "department";
    const COUNTY = "county";
    const AREA = "area";
    const PREFECTURE = "prefecture";
    const DO_SI = "do_si";
    const DISTRICT = "district";
    const OBLAST = "oblast";
}
