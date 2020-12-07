<?php


namespace App\Models\Traits;

use CommerceGuys\Addressing\Country\CountryRepositoryInterface;


trait HasCountryCode
{
    /**
     * Returns the country_code value, or a default value if the value is not set yet.
     *
     * @param $value
     * @return string
     */
    public function getCountryCodeAttribute($value)
    {
        return \Parse::try('NL')->countryCode($value);
    }

    /**
     * Saves the country_code in the right format.
     *
     * @param $newValue
     */
    public function setCountryCodeAttribute($newValue) {
        $this->attributes['country_code'] = mb_strtoupper(substr(trim($newValue),'0','2'));
    }

    /**
     * Returns the full, Dutch name of the country.
     *
     * @return mixed
     */
    public function getCountryAttribute() {
        $code = $this->country_code;
        $list = app(CountryRepositoryInterface::class)->getList('NL');
        return \Arr::get($list, $code);
    }
}
