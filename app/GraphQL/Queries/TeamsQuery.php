<?php


namespace App\GraphQL\Queries;

use App\Models\Team;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TeamsQuery extends Query
{
    protected $attributes = [
        'name' => 'teams',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('team'));
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, \Closure $getSelectedFields) {
        return Team::all();
    }
}
