<?php

namespace App\Services\Parsers;


use Carbon\Carbon;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\ValueNode;

/**
 * Class ParseService
 * @package App\Services\Parsers
 */
class ParseService
{

    /**
     * Creates an ParseWithAlternative instance. This will also parse the input, but will return a default value
     * instead of throwing an error on failure.
     *
     * @param null $default
     * @return ParseWithAlternative
     */
    public function try($default = null) {
        return new ParseWithAlternative($this, $default);
    }


    // ---------------------------------------------------------------------------------------------------------- //
    // ----- DATE ----------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Function that tries to return a Carbon instance.
     *
     * @param mixed $input
     * @param boolean $nowOnNull
     * @return \Carbon\Carbon|null
     */
    public function date($input, $nowOnNull = false)
    {
        if($input instanceof Carbon) {
            return $input;
        }

        if($input instanceof \DateTime) {
            return Carbon::instance($input);
        }

        if($input instanceof ValueNode) {
            if($input instanceof StringValueNode) {
                return $this->date(strval($input->value), $nowOnNull);
            } elseif ($input instanceof IntValueNode) {
                return $this->date(intval($input->value), $nowOnNull);
            } elseif ($input instanceof FloatValueNode) {
                return $this->date(floatval($input->value), $nowOnNull);
            } else {
                throw new NotParsableException('date', $input);
            }
        }

        if(is_string($input)) {
            try {
                return Carbon::parse($input);
            } catch (\Exception $e) {
                throw new NotParsableException('date', $input, 0, $e);
            }
        }

        if(is_integer($input)) {
            return Carbon::createFromTimestamp($input);
        }

        if(is_float($input)) {
            return Carbon::createFromTimestampMs(round($input * 100));
        }


        if($nowOnNull && $input === null) {
            return Carbon::now();
        }

        throw new NotParsableException('date', $input);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- COUNTRY CODE --------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * A list of all the countries and there names.
     *
     * @var array
     */
    protected $countryList = null;

    /**
     * A function that tries to return a valid country-code string.
     *
     * @param mixed $input
     * @param boolean $checkWithRepository
     * @return string
     */
    public function countryCode($input, $checkWithRepository = false)
    {
        if(is_string($input)) {
            $val = $input;
        } elseif(is_object($input) && method_exists($input, '__toString')) {
            $val = strval($input);
        } else {
            throw new NotParsableException('country_code', $input);
        }

        $val = mb_strtoupper(trim($val));

        if(strlen($val) !== 2) {
            throw new NotParsableException('country_code', $input);
        }

        if($checkWithRepository) {
            if($this->countryList === null) {
                $this->countryList = app(CountryRepositoryInterface::class)->getList('nl');
            }

            if(!array_key_exists($val, $this->countryList)) {
                throw new NotParsableException('country_code', $input);
            }
        }

        return $val;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- INTEGER -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * A function that tries to return a valid integer from the provided input.
     *
     * @param mixed $input
     * @return int
     */
    public function int($input)
    {
        if(is_integer($input)) {
            return $input;
        }

        if(is_string($input)) {
            $trimmed = trim($input);
            if(ctype_digit($trimmed)) {
                return intval($trimmed);
            } else {
                throw new NotParsableException('int', $input);
            }
        }

        if(is_numeric($input)) {
            return intval(round($input));
        }

        throw new NotParsableException('int', $input);
    }

}
