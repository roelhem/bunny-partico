<?php


namespace App\Services\StaticData;


use libphonenumber\PhoneNumberUtil;

class PhoneNumberFormat
{
    protected $region_code;
    protected $prefix;

    public function __construct(string $region_code, int $prefix)
    {
        $this->region_code = $region_code;
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getRegionCode()
    {
        return $this->region_code;
    }

    public function getSupportedTypes()
    {
        return resolve(PhoneNumberUtil::class)->getSupportedTypesForRegion($this->region_code);
    }
}
