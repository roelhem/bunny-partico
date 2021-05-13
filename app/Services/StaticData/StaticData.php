<?php


namespace App\Services\StaticData;


use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use CommerceGuys\Intl\Language\LanguageRepositoryInterface;
use libphonenumber\PhoneNumberUtil;

class StaticData
{
    public function getCountry(string $countryCode, ?string $locale = null) {
        /** @var CountryRepositoryInterface $repository */
        $repository = resolve(CountryRepositoryInterface::class);
        return $repository->get($countryCode, $locale);
    }

    public function getCountries(?string $locale = null) {
        /** @var CountryRepositoryInterface $repository */
        $repository = resolve(CountryRepositoryInterface::class);
        return $repository->getAll($locale);
    }

    public function getAddressFormat(string $countryCode) {
        /** @var AddressFormatRepositoryInterface $repository */
        $repository = resolve(AddressFormatRepositoryInterface::class);
        return $repository->get($countryCode);
    }

    public function getAddressFormats() {
        /** @var AddressFormatRepositoryInterface $repository */
        $repository = resolve(AddressFormatRepositoryInterface::class);
        return $repository->getAll();
    }

    public function getAddressLocalityTypes() {
        $formats = collect($this->getAddressFormats());
        return $formats->map(function (AddressFormat $format) {
            return $format->getLocalityType();
        })->unique();
    }

    public function getAddressDependentLocalityTypes() {
        $formats = collect($this->getAddressFormats());
        return $formats->map(function (AddressFormat $format) {
            return $format->getDependentLocalityType();
        })->unique();
    }

    public function getAddressAdministrativeAreaTypes() {
        $formats = collect($this->getAddressFormats());
        return $formats->map(function (AddressFormat $format) {
            return $format->getAdministrativeAreaType();
        })->unique();
    }

    public function getAddressPostalCodeTypes() {
        $formats = collect($this->getAddressFormats());
        return $formats->map(function (AddressFormat $format) {
            return $format->getPostalCodeType();
        })->unique();
    }

    public function getSubdivision(string $subdivisionCode, array $parents) {
        $repository = resolve(SubdivisionRepositoryInterface::class);
        return $repository->get($subdivisionCode, $parents);
    }

    public function getSubdivisions(array $parents) {
        $repository = resolve(SubdivisionRepositoryInterface::class);
        return $repository->getAll($parents);
    }

    public function getCurrency(string $currencyCode, ?string $locale = null) {
        /** @var CurrencyRepositoryInterface $repository  */
        $repository = resolve(CurrencyRepositoryInterface::class);
        return $repository->get($currencyCode, $locale);
    }

    public function getCurrencies(?string $locale = null) {
        /** @var CurrencyRepositoryInterface $repository  */
        $repository = resolve(CurrencyRepositoryInterface::class);
        return $repository->getAll($locale);
    }

    public function getLanguage(string $languageCode, ?string $locale = null) {
        /** @var LanguageRepositoryInterface $repository  */
        $repository = resolve(LanguageRepositoryInterface::class);
        return $repository->get($languageCode, $locale);
    }

    public function getLanguages(?string $locale = null) {
        /** @var LanguageRepositoryInterface $repository  */
        $repository = resolve(LanguageRepositoryInterface::class);
        return $repository->getAll($locale);
    }

    /**
     * @param string $region_code
     * @return \libphonenumber\PhoneMetadata|null
     */
    public function getPhoneNumberMeta(string $region_code) {
        /** @var PhoneNumberUtil $util */
        $util = resolve(PhoneNumberUtil::class);
        return $util->getMetadataForRegion($region_code);
    }
}
