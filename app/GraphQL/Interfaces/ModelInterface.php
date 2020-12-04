<?php


namespace App\GraphQL\Interfaces;


use App\GraphQL\Fields\FormattableDateField;
use Illuminate\Database\Eloquent\Model;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InterfaceType;
use GraphQL\Type\Definition\Type;

class ModelInterface extends InterfaceType
{
    protected $attributes = [
        'name' => 'Model',
        'description' => 'A model from the database.',
    ];

    public function types(): array
    {
        return [
            GraphQL::type('user'),
            GraphQL::type('team'),
        ];
    }

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The ID of this Model',
                'alias' => 'global_id',
            ],
            'class' => [
                'type' => Type::string(),
                'description' => 'The class of data to which this model belongs.',
            ],
            'createdAt' => (new FormattableDateField([
                'alias' => 'created_at',
                'description' => 'The date on which this _Model_ was added.',
            ]))->toArray(),
            'updatedAt' => (new FormattableDateField([
                'alias' => 'updated_at',
                'description' => 'The last date on which this _Model_ was modified.',
            ]))->toArray(),
        ];
    }

    public function resolveType(Model $root)
    {
        switch ($root->getTable()) {
            case 'users': return GraphQL::type('user');
            case 'teams': return GraphQL::type('team');
        }
    }
}
