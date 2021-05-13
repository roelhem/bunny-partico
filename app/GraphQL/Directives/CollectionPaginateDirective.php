<?php

namespace App\GraphQL\Directives;

use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Pagination\LengthAwarePaginator;
use Nuwave\Lighthouse\Pagination\PaginationArgs;
use Nuwave\Lighthouse\Pagination\PaginationManipulator;
use Nuwave\Lighthouse\Pagination\PaginationType;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\DefinedDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CollectionPaginateDirective extends BaseDirective implements FieldMiddleware, FieldManipulator, DefinedDirective
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
"""
Query multiple entries in a Laravel collection
"""
directive @collectionPaginate(
  """
  Which pagination style to use.
  Allowed values: `paginator`, `connection`.
  """
  type: String = "paginator"

  """
  Allow clients to query paginated lists without specifying the amount of items.
  Overrules the `pagination.default_count` setting from `lighthouse.php`.
  """
  defaultCount: Int

  """
  Limit the maximum amount of items that clients can request from paginated lists.
  Overrules the `pagination.max_count` setting from `lighthouse.php`.
  """
  maxCount: Int
) on FIELD_DEFINITION
SDL;
    }

    /**
     * Set a field resolver on the FieldValue.
     *
     * This must call $fieldValue->setResolver() before returning
     * the FieldValue.
     *
     * @param  FieldValue  $fieldValue
     * @param  \Closure $next
     * @return FieldValue
     */
    public function handleField(FieldValue $fieldValue, \Closure $next)
    {
        $prevResolver = $fieldValue->getResolver();

        $wrappedResolver =function ($root,
                                    array $args,
                                    GraphQLContext $context,
                                    ResolveInfo $resolveInfo) use ($prevResolver): LengthAwarePaginator  {
            $paginationArgs = PaginationArgs::extractArgs($args, $this->paginationType(), $this->paginateMaxCount());

            $collection = collect($prevResolver($root, $args, $context, $resolveInfo));
            $total = $collection->count();
            $pageSize = $paginationArgs->first;
            $page = $paginationArgs->page;
            $offset = ($page - 1) * $pageSize;
            $items = $collection->slice($offset, $pageSize);
            return new LengthAwarePaginator($items, $total, $pageSize, $page);
        };

        $fieldValue->setResolver($wrappedResolver);
        return $next($fieldValue);
    }

    /**
     * Manipulate the AST based on a field definition.
     *
     * @param  \Nuwave\Lighthouse\Schema\AST\DocumentAST  $documentAST
     * @param  \GraphQL\Language\AST\FieldDefinitionNode  $fieldDefinition
     * @param  \GraphQL\Language\AST\ObjectTypeDefinitionNode  $parentType
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     * @return void
     */
    public function manipulateFieldDefinition(
        DocumentAST &$documentAST,
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode &$parentType
    ) {
        $manipulator = new PaginationManipulator($documentAST);

        $manipulator->transformToPaginatedField(
            $this->paginationType(),
            $fieldDefinition,
            $parentType,
            $this->directiveArgValue('defaultCount')
            ?? config('lighthouse.pagination.default_count'),
            $this->paginateMaxCount()
        );
    }

    /**
     * @return PaginationType
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     */
    protected function paginationType(): PaginationType
    {
        return new PaginationType(
            $this->directiveArgValue('type', PaginationType::TYPE_PAGINATOR)
        );
    }

    /**
     * Get either the specific max or the global setting.
     */
    protected function paginateMaxCount(): ?int
    {
        return $this->directiveArgValue('maxCount')
            ?? config('lighthouse.pagination.max_count')
            ?? config('lighthouse.paginate_max_count');
    }
}
