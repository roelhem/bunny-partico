<?php


namespace App\GraphQL\Types\Connections;
use App\GraphQL\Fields\ConnectionField;
use App\Services\CursorPagination\CursorPaginator;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;


class ConnectionType extends GraphQLType
{

    /**
     * @var ConnectionField The field to which this ConnectionType is connected.
     */
    protected $connectionField;

    /**
     * ConnectionType constructor.
     *
     * @param ConnectionField $connectionField
     */
    public function __construct(ConnectionField $connectionField)
    {
        $this->connectionField = $connectionField;
    }

    /**
     * Prefix for the type-names associated with this connection.
     *
     * Basically it just forwards the `connectionName` function of the `$connectionField` of this ConnectionType.
     *
     * @return string
     */
    public function connectionName(): string
    {
        return $this->connectionField->connectionName();
    }

    /**
     * The type that the end nodes of this connection should have.
     *
     * Basically it just forwards the `to` property of the `$connectionField` of this ConnectionType.
     *
     * @return Type
     */
    public function nodeType(): Type
    {
        return $this->connectionField->to;
    }

    /**
     * Dynamically generated attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => $this->connectionField->connectionName() . 'Connection',
        ];
    }

    /**
     * The fields of this ConnectionType.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            'edges' => [
                'type' => Type::nonNull(Type::listOf($this->edgeType())),
                'description' => 'Edges of the connection.',
            ],
            'pageInfo' => [
                'type' => Type::nonNull($this->pageInfoType()),
                'description' => 'Information about page currently shown.',
            ],
        ];
    }

    /**
     * The type for the edges.
     *
     * @return Type
     */
    protected function edgeType(): Type
    {
        return new ObjectType([
            'name' => $this->connectionName() .'Edge',
            'fields' => [
                'cursor' => [
                    'type' => GraphQL::type('Cursor!'),
                    'description' => 'The cursor identifying this edge',
                ],
                'node' => [
                    'type' => $this->nodeType(),
                    'description' => 'The node to which this edge points.',
                ],
            ]
        ]);
    }

    /**
     * The type for the `pageInfo` field.
     *
     * @return NullableType
     */
    protected function pageInfoType(): NullableType
    {
        return new ObjectType([
            'name' => $this->connectionName() . 'PageInfo',
            'fields' => [
                'hasPreviousPage' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether or not this connection has a previous page.',
                ],
                'hasNextPage' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether or not this connection has a next page.',
                ],
                'startCursor' => [
                    'type' => GraphQL::type('Cursor'),
                    'description' => 'The cursor of the first _Edge_ on this page.',
                ],
                'endCursor' => [
                    'type' => GraphQL::type('Cursor'),
                    'description' => 'the cursor of the last _Edge_ on this page.',
                ],
            ]
        ]);
    }

    /**
     * Resolver for the `edges` field.
     *
     * @param CursorPaginator $root
     * @param $args
     * @return array
     */
    public function resolveEdgesField(CursorPaginator $root, $args): array
    {
        return $root->items()->map(function ($value, $key) use ($root) {
            return [
                'node' => $value,
                'cursor' => $root->getCursor($value),
            ];
        })->all();
    }

    /**
     * Resolver for the `pageInfo` field.
     *
     * @param CursorPaginator $root
     * @param $args
     * @return array
     */
    public function resolvePageInfoField(CursorPaginator $root, $args): array
    {
        return [
            'hasPreviousPage' => $root->hasPrev(),
            'hasNextPage' => $root->hasNext(),
            'startCursor' => $root->firstCursor(),
            'endCursor' => $root->lastCursor(),
        ];
    }
}
