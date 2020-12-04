<?php


namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;


class MeQuery extends Query
{
    protected $attributes = [
        'name' => 'me',
    ];

    public function type(): Type
    {
        return GraphQL::type('user');
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, \Closure $getSelectedFields) {
        return \Auth::user();
    }
}
