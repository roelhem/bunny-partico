<?php

namespace App\GraphQL\Directives;

use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CountryDirective extends BaseDirective implements FieldResolver
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    /**
     * Set a field resolver on the FieldValue.
     *
     * This must call $fieldValue->setResolver() before returning
     * the FieldValue.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $fieldValue
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function resolveField(FieldValue $fieldValue)
    {
        $resolver = $fieldValue->getResolver();

        $fieldValue->setResolver(function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {

            $value = $resolver($root, $args, $context, $resolveInfo);

            $repository = resolve(CountryRepositoryInterface::class);

            return $repository->get($value->countryCode);
        });

        return $fieldValue;
    }
}
