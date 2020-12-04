<?php


namespace App\GraphQL\Types\Models;

use App\GraphQL\Fields\ConnectionField;
use App\GraphQL\Fields\FormattableDateField;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Illuminate\Database\Eloquent\Model;


abstract class ModelType extends GraphQLType
{
    /**
     * Creates a new ConnectionField for this model. Should be used in the [[fields]] method.
     *
     * @param $type string|Type The type to which this ModelType should be connected.
     * @param array $settings Additional attributes for this ConnectionField.
     * @return ConnectionField The resulting ConnectionField that can be used in the [[fields]] method.
     */
    protected function connection($type, $settings = []): ConnectionField
    {
        if(is_string($type)) {
            $type = GraphQL::type($type);
        }
        return new ConnectionField($this, $type, $settings);
    }





    protected function getAdditionalField(string $name, array $field = [])
    {
        $resolver = $this->getFieldResolver($name, $field);
        if($resolver) {
            $field['resolve'] = $resolver;
        }
        return $field;
    }

    public function getModelName(): string {
        $attributes = array_merge($this->attributes, $this->attributes());
        return $attributes['name'];
    }

    public function getFields(): array
    {
        $fields = parent::getFields();
        return array_merge([
            'id' => $this->getAdditionalField('id', [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The ID of this _' . $this->getModelName() .'_',
                'alias' => 'global_id',
            ]),
            'class' => $this->getAdditionalField('class', [
                'type' => Type::string(),
                'description' => 'The class of data to which this model belongs.',
                'resolve' => function(Model $root, $args) {
                    return $root->getTable();
                },
                'selectable' => false,
            ]),
        ], $fields, [
            'createdAt' => (new FormattableDateField([
                'alias' => 'created_at',
                'description' => 'The date on which this _' . $this->getModelName() . '_ was added.',
            ]))->toArray(),
            'updatedAt' => (new FormattableDateField([
                'alias' => 'updated_at',
                'description' => 'The last date on which this _' . $this->getModelName() . '_ was modified.',
            ]))->toArray(),
        ]);
    }

    public function getAttributes(): array
    {
        $result = parent::getAttributes();
        $extraInterfaces = [
            GraphQL::type('model'),
        ];
        if(array_key_exists('interfaces', $result)) {
            $result['interfaces'] = array_merge($result['interfaces'], $extraInterfaces);
        } else {
            $result['interfaces'] = $extraInterfaces;
        }
        return $result;
    }
}
