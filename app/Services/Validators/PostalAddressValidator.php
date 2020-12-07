<?php
/**
 * Created by PhpStorm.
 * User: roel
 * Date: 04-06-18
 * Time: 19:19
 */

namespace App\Services\Validators;


use App\Services\Parsers\NotParsableException;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Illuminate\Validation\Validator;

/**
 * Class AddressValidation
 *
 * Defines methods to help with the validation of a postal address.
 *
 * @package App\Services\Validation
 */
class PostalAddressValidator
{

    /**
     * @var CountryRepositoryInterface
     */
    protected $countryRepository;

    /**
     * @var AddressFormatRepositoryInterface
     */
    protected $addressFormatRepository;

    /**
     * @var SubdivisionRepositoryInterface
     */
    protected $subdivisionRepository;

    /**
     * The fields that should be ignored by the validator.
     *
     * @var array
     */
    protected $ignoredFields = [AddressField::ADDITIONAL_NAME, AddressField::GIVEN_NAME, AddressField::FAMILY_NAME];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- INITIALIZATION ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * AddressValidation constructor.
     * @param CountryRepositoryInterface $countryRepository
     * @param AddressFormatRepositoryInterface $addressFormatRepository
     * @param SubdivisionRepositoryInterface $subdivisionRepository
     */
    public function __construct( CountryRepositoryInterface $countryRepository,
                                 AddressFormatRepositoryInterface $addressFormatRepository,
                                 SubdivisionRepositoryInterface $subdivisionRepository ) {
        $this->countryRepository       = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository   = $subdivisionRepository;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- HELPERS -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Checks if a given value is a valid country_code string.
     *
     * @param $value
     * @return bool
     */
    protected function isCountryCode($value) {
        try {
            \Parse::countryCode($value, true);
            return true;
        } catch (NotParsableException $notParsableException) {
            return false;
        }
    }

    /**
     * Gives the full attribute name of a sibbling of the given $attribute
     *
     * @param string $attribute
     * @param string $sibling
     * @return string
     */
    protected function siblingAttributeName($attribute, $sibling) {
        $attributePieces = explode('.', $attribute);
        array_pop($attributePieces);
        array_push($attributePieces, $sibling);
        return implode('.',$attributePieces);
    }

    /**
     * Gives the short name of the attribute.
     *
     * @param $attribute
     * @return mixed
     */
    protected function shortAttributeName($attribute) {
        return \Arr::last(explode('.',$attribute));
    }

    /**
     * Gives a valid countryCode based on the parameters of a validation.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param Validator $validator
     * @return string
     */
    protected function getCountryCode($attribute, $value, $parameters, $validator) {
        // Try to get if from the the value itself
        if(is_array($value) && \Arr::has($value, 'country_code')) {
            return $value['country_code'];
        }
        // Try to get if from a sibling attribute
        $countryCodeAttributeName = $this->siblingAttributeName($attribute, 'country_code');
        $countryCode = \Arr::get($validator->getData(), $countryCodeAttributeName);
        try {
            return \Parse::countryCode($countryCode, true);
        } catch (NotParsableException $notParsableException) {}

        // If given, use the default codes in the parameter
        foreach ($parameters as $parameter) {
            try {
                return \Parse::countryCode($parameter, true);
            } catch (NotParsableException $notParsableException) {}
        }
        // Return the global default country code.
        return 'NL';
    }

    /**
     * Returns the attribute in a format that matches the field-type names of the addressing package.
     *
     * @param $attribute
     * @return string
     */
    protected function getFieldType($attribute) {
        return \Str::camel($this->shortAttributeName($attribute));
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- VALIDATE METHODS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Checks if a given string is an valid country_code.
     *
     * This function will just check if the given value is in the list of the country-repository.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param Validator $validator
     * @return boolean
     */
    public function validateCountryCode($attribute, $value, $parameters, $validator) {
        return $this->isCountryCode($value);
    }


    /**
     * Checks if a the validated field is a required address_field
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param Validator $validator
     * @return bool
     */
    public function validateAddressField($attribute, $value, $parameters, $validator) {
        // Get the country_code and corresponding addressFormat.
        $countryCode = $this->getCountryCode($attribute, $value, $parameters, $validator);
        $addressFormat = $this->addressFormatRepository->get($countryCode);
        // Check if the attribute is required.
        $requiredFields = array_diff($addressFormat->getRequiredFields(), $this->ignoredFields);
        if(in_array($this->getFieldType($attribute), $requiredFields)) {
            return is_string($value) && !empty($value);
        }

        if(!in_array('allow_unused', $parameters)) {
            // Check if the attribute is used.
            $usedFields = array_diff($addressFormat->getUsedFields(), $this->ignoredFields);
            if (in_array($this->getFieldType($attribute), $usedFields)) {
                return true;
            } else {
                return empty($value);
            }
        } else {
            return true;
        }
    }

    /**
     * Check if the validated field matches the postal code pattern of the addressFormat that belongs
     * to the associated country_code.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param Validator $validator
     * @return bool
     */
    public function validatePostalCode($attribute, $value, $parameters, $validator) {
        // Get the country_code and corresponding addressFormat.
        $countryCode = $this->getCountryCode($attribute, $value, $parameters, $validator);
        $addressFormat = $this->addressFormatRepository->get($countryCode);
        // Get the pattern
        $pattern = $addressFormat->getPostalCodePattern();
        // Check if the value conforms to the pattern
        preg_match('/'.$pattern.'/i', $value, $matches);
        if(!isset($matches[0]) || $matches[0] !== $value) {
            return false;
        } else {
            return true;
        }

    }


}
