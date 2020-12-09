<?php


namespace App\Models\Traits;


/**
 * Trait HasCountryCode
 * @package App\Models\Traits
 *
 * @property-read string $country_code
 */
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
     * @return \CommerceGuys\Addressing\Country\Country|null
     */
    public function getCountryAttribute() {
        return \StaticData::getCountry($this->country_code);
    }
}
