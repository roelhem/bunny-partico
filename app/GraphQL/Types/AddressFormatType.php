<?php


namespace App\GraphQL\Types;


use App\Enums\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;

class AddressFormatType
{
    // ---------------------------------------------------------------------------------------------------------- //
    // ----- PARSING -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function __invoke($countryCode)
    {
        return $this->getInstance($countryCode);
    }

    public function getInstance($input): AddressFormat
    {
        if($input instanceof AddressFormat) {
            return $input;
        } else {
            $countryCode = \Parse::countryCode($input);
            return \StaticData::getAddressFormat($countryCode);
        }
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- INITIALISATION ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function resolveCountry($root)
    {
        return $this->getInstance($root)->getCountryCode();
    }

    public function resolveCountryCode($root)
    {
        return $this->getInstance($root)->getCountryCode();
    }

    public function resolveFormat($root, $args)
    {
        // Parse args.
        $local    = \Arr::get($args, 'local', false);
        $withName = \Arr::get($args, 'withName', false);

        $instance = $this->getInstance($root);
        $formatString = $local ? $instance->getFormat() : $instance->getLocalFormat();
        if($formatString === null) {
            $formatString = $instance->getFormat();
        }

        return AddressField::getFormatFields($formatString, $withName);
    }
}
