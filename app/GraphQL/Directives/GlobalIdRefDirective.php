<?php

namespace App\GraphQL\Directives;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GlobalIdRefDirective extends BaseDirective implements FieldMiddleware
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    /**
     * Wrap around the final field resolver.
     *
     * @param \Nuwave\Lighthouse\Schema\Values\FieldValue $fieldValue
     * @param \Closure $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $fieldValue, \Closure $next)
    {
        $resolver = $fieldValue->getResolver();

        $fieldValue->setResolver(function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {

            $value = $resolver($root, $args, $context, $resolveInfo);

            if($value === null) {
                return $value;
            }

            $fullString = $this->directiveArgValue('type').":".$value;
            return base64_encode($fullString);
        });

        return $next($fieldValue);
    }
}
