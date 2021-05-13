<?php


namespace App\GraphQL\Directives;


use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\Parser;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\NodeDirective;

class GeneralNodeDirective extends NodeDirective
{

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
"""
Register a type for Relay's global object identification.
When used without any arguments.
"""
directive @generalNode(
  """
  Reference to resolver function.
  Consists of two parts: a class name and a method name, seperated by an `@` symbol.
  If you pass only a class name, the method name defaults to `__invoke`.
  """
  resolver: String
) on FIELD_DEFINITION
SDL;
    }


    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition): void
    {
        $typeDefinition->interfaces = ASTHelper::mergeNodeList(
            $typeDefinition->interfaces,
            [
                Parser::parseType(
                    'Node',
                    ['noLocation' => true]
                ),
            ]
        );
    }
}
