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

class AccessControlDirective extends BaseDirective implements TypeManipulator, TypeExtensionManipulator
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html
    protected function getAccessControlFields()
    {
        return [
            PartialParser::fieldDefinition(<<<'GRAPHQL'
"""
The access level that the user currently has on this instance.
"""
my_access_level: AccessLevel @method(name: "authAccessLevel")
GRAPHQL),
            PartialParser::fieldDefinition(<<<'GRAPHQL'
"""
The minimal access level that a user need to have a certain ability.
"""
access_level(
    """
    The ability of which you want to know the minimal access level.
    """
    ability: String!
): AccessLevel @method(name: "getAccessLevel", passOrdered: true)
GRAPHQL),
            PartialParser::fieldDefinition(<<<'GRAPHQL'
"""
A connection to the permission flag instances that determine the minimal access levels on this instance.
"""
permission_flags: [PermissionFlag] @morphMany(relation: "permissionFlags")
GRAPHQL)];
    }

    /**
     * Apply manipulations from a type definition node.
     *
     * @param  \Nuwave\Lighthouse\Schema\AST\DocumentAST  $documentAST
     * @param  \GraphQL\Language\AST\TypeDefinitionNode  $typeDefinition
     * @return void
     */
    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition)
    {
        $typeDefinition->fields = ASTHelper::mergeNodeList($typeDefinition->fields, $this->getAccessControlFields());
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
        $typeExtension->fields = ASTHelper::mergeNodeList($typeExtension->fields, $this->getAccessControlFields());
    }
}
