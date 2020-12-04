<?php


namespace App\GraphQL\Types\Models;

use App\GraphQL\Fields\AssetUrlField;
use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;


class UserType extends ModelType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'email' => [
                'type' => Type::string(),
                'description' => 'The email-address of the user',
            ],
            'emailVerified' => [
                'type' => Type::boolean(),
                'description' => 'Whether or not the email-address of this user is verified',
                'resolve' => function(User $root, $args) {
                    return $root->hasVerifiedEmail();
                }
            ],
            'ownedTeams' => $this->connection('team', [
                'description' => 'The teams of which this user is a member of.',
            ]),
            'personalTeam' => [
                'type' => GraphQL::type('team'),
                'description' => 'The personal team of this user.',
                'resolve' => function (User $root, $args) {
                    return $root->personalTeam();
                }
            ],
            'currentTeam' => [
                'type' => GraphQL::type('team'),
                'description' => 'The team that is currently active for this user',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the user',
            ],
            'profilePhoto' => new AssetUrlField([
                'alias' => 'profile_photo_path',
                'description' => 'The _URI_ to the profile photo of this user.',
            ]),
        ];
    }
}
