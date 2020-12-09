<?php


namespace App\GraphQL\Types;


use CommerceGuys\Addressing\Country\Country;

class CountryType
{
    /**
     * @param $input
     * @param string|null $locale
     * @return Country
     */
    protected function getCountryInstance($input, ?string $locale = null)
    {
        if($input instanceof Country) {
            if($locale && $input->getLocale() !== $locale) {
                return \StaticData::getCountry($input->getCountryCode(), $locale);
            } else {
                return $input;
            }
        } else {
            $countryCode = \Parse::countryCode($input);
            return \StaticData::getCountry($countryCode, $locale);
        }
    }

    public function resolveName($root, array $args)
    {
        return $this->getCountryInstance($root, $args['locale'])->getName();
    }

    public function resolveCountryCode($root)
    {
        return $this->getCountryInstance($root)->getCountryCode();
    }

    public function resolveThreeLetterCode($root)
    {
        return $this->getCountryInstance($root)->getThreeLetterCode();
    }

    public function resolveNumericCode($root)
    {
        return $this->getCountryInstance($root)->getThreeLetterCode();
    }

    public function resolveCurrencyCode($root)
    {
        return $this->getCountryInstance($root)->getCurrencyCode();
    }

    public function resolveAddressFormat($root)
    {
        return \StaticData::getAddressFormat($this->getCountryInstance($root)->getCountryCode());
    }

    public function resolvePhoneNumberFormat($root)
    {
        return \StaticData::getPhoneNumberMeta($this->getCountryInstance($root)->getCountryCode());
    }
}
