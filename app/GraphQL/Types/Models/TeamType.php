<?php


namespace App\GraphQL\Types\Models;

use App\Models\Team;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;


class TeamType extends ModelType
{
    protected $attributes = [
        'name' => 'Team',
        'description' => 'A team of users',
        'model' => Team::class,
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the _Team_.',
            ],
            'owner' => [
                'type' => GraphQL::type('user'),
                'description' => 'The owner of this team.',
            ],
            'isPersonalTeam' => [
                'type' => Type::boolean(),
                'description' => 'Whether or not this team is a personal team.',
                'alias' => 'personal_team',
            ],
        ];
    }
}
