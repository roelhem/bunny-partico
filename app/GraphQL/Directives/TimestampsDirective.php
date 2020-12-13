<?php

namespace App\GraphQL\Directives;

use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\AST\TypeExtensionNode;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\AST\PartialParser;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\TypeExtensionManipulator;
use Nuwave\Lighthouse\Support\Contracts\TypeManipulator;

class TimestampsDirective extends BaseDirective implements TypeManipulator, TypeExtensionManipulator
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    /**
     * Apply manipulations from a type definition node.
     *
     * @param  \Nuwave\Lighthouse\Schema\AST\DocumentAST  $documentAST
     * @param  \GraphQL\Language\AST\TypeDefinitionNode  $typeDefinition
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     * @return void
     */
    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition)
    {
        // TODO implement the type manipulator
        $typeDefinition->fields = ASTHelper::mergeNodeList($typeDefinition->fields, [
            PartialParser::fieldDefinition("created_at: DateTime"),
            PartialParser::fieldDefinition("updated_at: DateTime"),
        ]);
    }

    /**
     * Apply manipulations from a type extension node.
     *
     * @param  \Nuwave\Lighthouse\Schema\AST\DocumentAST  $documentAST
     * @param  \GraphQL\Language\AST\TypeExtensionNode  $typeExtension
     * @return void
     */
    public function manipulateTypeExtension(DocumentAST &$documentAST, TypeExtensionNode &$typeExtension)
    {
        // TODO implement the type extension manipulator
        $typeExtension->fields = ASTHelper::mergeNodeList($typeExtension->fields, [
            PartialParser::fieldDefinition("created_at: DateTime"),
            PartialParser::fieldDefinition("updated_at: DateTime"),
        ]);
    }
}
