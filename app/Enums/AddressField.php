<?php


namespace App\Enums;


use BenSampo\Enum\Enum;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use phpDocumentor\Reflection\Types\Static_;

final class AddressField extends Enum
{
    // ---------------------------------------------------------------------------------------------------------- //
    // ----- VALUES --------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    // Address
    const ADDRESS_LINE_1 = 'addressLine1';
    const ADDRESS_LINE_2 = 'addressLine2';
    const ADMINISTRATIVE_AREA = 'administrativeArea';
    const LOCALITY = 'locality';
    const DEPENDENT_LOCALITY = 'dependentLocality';
    const POSTAL_CODE = 'postalCode';
    const SORTING_CODE = 'sortingCode';

    // Name
    const ORGANISATION = 'organization';
    const GIVEN_NAME = 'givenName';
    const ADDITIONAL_NAME = 'additionalName';
    const FAMILY_NAME = 'familyName';

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- STATIC GETTERS ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public static function getDatabaseFields()
    {
        $result = [];
        foreach (self::getInstances() as $instance) {
            $field = $instance->getDatabaseField();
            if($field !== null) {
                array_push($result, $instance->getDatabaseField());
            }
        }
        return $result;
    }

    public static function fromFuzzyValue($value)
    {
        if(!static::hasValue($value)) {
            foreach (static::getValues() as $knownValue) {
                if(str_starts_with($value, $knownValue)) {
                    $value = $knownValue;
                    break;
                }
            }
        }
        return static::fromValue($value);
    }

    public static function getFormatFields($formatString, $withName = false)
    {
        if($formatString === null) {
            return null;
        }

        $result = [];
        $lines = explode("\n", $formatString);

        foreach($lines as $line) {
            $matches = [];
            if(preg_match_all("/%(?<field>\\w+)/", $line, $matches)) {
                $fields = $matches['field'];
                $lineResult = [];
                foreach ($fields as $field) {
                    $instance = self::fromFuzzyValue($field);
                    if($withName || !$instance->isNameField()) {
                        $lineResult[] = $instance;
                    }
                }
                if(count($lineResult) > 0) {
                    $result[] = $lineResult;
                }
            }
        }

        return $result;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- GETTERS -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function getDatabaseField()
    {
        switch ($this->value) {
            case self::ADDRESS_LINE_1: return 'address_line_1';
            case self::ADDRESS_LINE_2: return 'address_line_2';
            case self::ADMINISTRATIVE_AREA: return 'administrative_area';
            case self::LOCALITY: return 'locality';
            case self::DEPENDENT_LOCALITY: return 'dependent_locality';
            case self::POSTAL_CODE: return 'postal_code';
            case self::SORTING_CODE: return 'sorting_code';
            case self::ORGANISATION: return 'organisation';
            default: return null;
        }
    }

    public function isNameField()
    {
        switch ($this->value) {
            case self::GIVEN_NAME:
            case self::ADDITIONAL_NAME:
            case self::FAMILY_NAME: return true;
            default: return false;
        }
    }
}
