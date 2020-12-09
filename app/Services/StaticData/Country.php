<?php


namespace App\Services\StaticData;


use CommerceGuys\Addressing\Country\CountryRepositoryInterface;

class Country
{

    /**
     * @var \CommerceGuys\Addressing\Country\Country
     */
    public $country;
    public $id;

    public function __construct(string $countryCode)
    {
        $repository = resolve(CountryRepositoryInterface::class);
        $this->country = $repository->get($countryCode);
        $this->id = $this->country->getCurrencyCode();
    }

    public function getName() {
        return $this->country->getName();
    }

    public function getCountryCode() {
        return $this->country->getCountryCode();
    }

    public function getThreeLetterCode() {
        return $this->country->getThreeLetterCode();
    }

    public function getNumericCode() {
        return $this->country->getNumericCode();
    }

    public function getCurrencyCode() {
        return $this->country->getCurrencyCode();
    }

    public function getLocale() {
        return $this->country->getLocale();
    }

}
