<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\Connections\ConnectionType;
use App\GraphQL\Types\Models\ModelType;
use App\Services\CursorPagination\CursorPaginator;
use GraphQL\Exception\InvalidArgument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Field;


class ConnectionField extends Field
{
    /**
     * The attributes of this field.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The type from which this connection flows away.
     *
     * @var ModelType
     */
    public $from;

    /**
     * The type of the end nodes of this connection.
     *
     * @var Type
     */
    public $to;

    /**
     * ConnectionField constructor.
     * @param ModelType $from The type that has this ConnectionField.
     * @param Type $to The type of the nodes to which this connection points.
     * @param array $settings Some additional settings for this ConnectionField.
     */
    public function __construct(ModelType $from, Type $to, $settings = [])
    {
        $this->from = $from;
        $this->to = $to;
        $this->attributes = array_merge($this->attributes, $settings);
    }

    /**
     * Dynamically generated attributes of this field.
     *
     * @return array
     */
    public function attributes(): array
    {
        $name = $this->attributes['name'] ?? $this->to->name;

        return [
            'description' => \Arr::get($this->attributes,
                'description',
                'Shows the connected _'.$name.'_ of this _'.$this->from->name.'_.'
            )
        ];
    }

    /**
     * Returns a new connection type for this ConnectionField.
     *
     * @return Type
     */
    public function type(): Type
    {
        $type = new ConnectionType($this);
        return $type->toType();
    }

    /**
     * The name of the connection. Can be used as a prefix to the associated Connection and Edge types.
     *
     * @return string
     */
    public function connectionName(): string
    {
        $fromName = $this->from->name;
        $toName = \Str::studly($this->to->name);
        return $fromName . $toName;
    }

    /**
     * Resolves this type.
     *
     * @param Model $root
     * @param $args
     * @return CursorPaginator
     */
    public function resolve(Model $root, $args)
    {
        $query = $this->getRelation($root)->getQuery();
        return CursorPaginator::create(
            $query,
            $this->getLimit($args),
            ['id' => 'asc'],
            \Arr::get($args, 'after', \Arr::get($args, 'before', [])),
            !\Arr::has($args, 'first') && \Arr::has($args, 'last'),
        );
    }

    /**
     * Gets the page size from the arguments.
     *
     * @param $args
     * @return mixed
     */
    protected function getLimit($args)
    {
        $limit = \Arr::get($args, 'first',
            \Arr::get($args, 'last',
                \Arr::get($this->attributes, 'defaultLimit', 15)
            )
        );

        if($limit <= 0) {
            throw new InvalidArgument('First/last has to be bigger than 0');
        }

        return $limit;
    }

    /**
     * The arguments for this ConnectionField.
     *
     * @return array
     */
    public function args(): array
    {
        return [
            'first' => [
                'type' => Type::int(),
                'description' => 'The amount of items you want to show.',
            ],
            'after' => [
                'type' => GraphQL::type('Cursor'),
                'description' => 'The cursor of the element after which you want to start the page.',
            ],
            'last' => [
                'type' => Type::int(),
                'description' => 'The amount of items you want to show.',
            ],
            'before' => [
                'type' => GraphQL::type('Cursor'),
                'description' => 'The cursor of the element before which you want to start the page.',
            ],
        ];
    }

    /**
     * Returns the relation that this ConnectionField should manage.
     *
     * @param Model $root
     * @return Relation
     */
    protected function getRelation(Model $root): Relation
    {
        $result = $root->{$this->getProperty()}();
        if(!($result instanceof Relation)) {
            return null;
        } else {
            return $result;
        }
    }

    /**
     * Returns the property on the $root model that should contain the relation that this ConnectionField manages.
     *
     * @return string
     */
    protected function getProperty(): string
    {
        return $this->attributes['alias'] ?? $this->attributes['name'];
    }
}
