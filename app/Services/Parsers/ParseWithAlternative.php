<?php

namespace App\Services\Parsers;

use Carbon\Carbon;

/**
 * Class ParseWithAlternative
 * @package App\Services\Parsers
 *
 *
 * @method Carbon|mixed date($input, bool $nowOnNull = false, $default = null)
 * @method string|mixed countryCode($input, $default = null)
 */
class ParseWithAlternative
{

    /**
     * @var ParseService
     */
    protected $service;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * ParseWithAlternative constructor.
     * @param ParseService $service
     * @param mixed $default
     */
    public function __construct(ParseService $service, $default = null)
    {
        $this->service = $service;
        $this->default = $default;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        try {
            $reflection = new \ReflectionClass($this->service);

            $method = $reflection->getMethod($name);
            $paramCount = $method->getNumberOfParameters();
            $argsCount = count($arguments);

            if($paramCount >= $argsCount) {
                $default = $this->default;
                $args = $arguments;
            } else {
                $default = $arguments[$paramCount];
                $args = array_slice($arguments, 0, $paramCount);
            }

            try {
                return $method->invokeArgs($this->service, $args);
            } catch (NotParsableException $notParsableException) {
                return $default;
            }

        } catch (\ReflectionException $reflectionException) {
            throw new \InvalidArgumentException("The method $name does not exist.", 0, $reflectionException);
        }
    }

}
