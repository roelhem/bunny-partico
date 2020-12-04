<?php

declare(strict_types=1);

namespace App\GraphQL\Scalars;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Language\AST\ListValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

class Cursor extends ScalarType implements TypeConvertible
{
    /**
     * @var string
     */
    public $name = 'Cursor';

    /**
     * @var string
     */
    public $description = 'A cursor in a _Connection_ object.';

    /**
     * Serializes an internal value to include in a response.
     *
     * @param array $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function serialize($value)
    {
        return $this->base64url_encode(json_encode($value));
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseValue($value)
    {
        if(!is_string($value)) {
            throw new Exception('Input of a cursor has to be a string.');
        }
        return json_decode($this->base64url_decode($value));
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node $valueNode
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return json_decode($this->base64url_decode($valueNode->value));
        } elseif($valueNode instanceof ListValueNode) {
            $result = [];
            foreach ($valueNode->values as $value) {
                if(isset($value->value)) {
                    $result[] = $value->value;
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    /**
     * Encode data to Base64URL
     * @param string $data
     * @return boolean|string
     */
    protected function base64url_encode($data)
    {
        // First of all you should encode $data to Base64 string
        $b64 = base64_encode($data);

        // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
        if ($b64 === false) {
            return false;
        }

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        $url = strtr($b64, '+/', '-_');

        // Remove padding character from the end of line and return the Base64URL result
        return rtrim($url, '=');
    }

    /**
     * Decode data from Base64URL
     * @param string $data
     * @param boolean $strict
     * @return boolean|string
     */
    protected function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }

    public function toType(): Type
    {
        return new static();
    }
}
