<?php


namespace App\GraphQL\Types\Connections;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class EdgeType extends GraphQLType
{
    /**
     * The associated ConnectionType.
     *
     * @var ConnectionType
     */
    protected $connectionType;

    /**
     * EdgeType constructor.
     * @param ConnectionType $connectionType The connection type that contains this edge type.
     */
    public function __construct(ConnectionType $connectionType)
    {
        $this->connectionType = $connectionType;
    }

    /**
     * Dynamically generated attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => $this->connectionType->connectionName(). 'Edge',
        ];
    }

    /**
     * Fields of this EdgeType.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            'cursor' => [
                'type' => Type::string(),
                'description' => 'The cursor identifying this edge',
            ],
            'node' => [
                'type' => $this->connectionType->nodeType(),
                'description' => 'The node to which this edge points.',
            ],
        ];
    }
}
