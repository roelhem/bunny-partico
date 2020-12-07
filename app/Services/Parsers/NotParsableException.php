<?php


namespace App\Services\Parsers;


class NotParsableException extends \InvalidArgumentException
{
    protected $method;
    protected $input;

    public function __construct(string $method, $input = null, int $code = 0, \Throwable $previous = null)
    {
        $this->method = $method;
        $this->input = $input;

        if(is_string($input)) {
            $inputStr = "'$input'";
        } elseif(is_numeric($input)) {
            $inputStr = strval($input);
        } elseif(is_bool($input)) {
            $inputStr = $input ? 'true' : 'false';
        } elseif(is_object($input)) {
            $inputStr = get_class($input);
        } else {
            $inputStr = gettype($input);
        }

        parent::__construct("Can't parse the input  $inputStr  with the parser $method.", $code, $previous);
    }
}
