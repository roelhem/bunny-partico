<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\ManipulateAST;
use Nuwave\Lighthouse\Schema\AST\PartialParser;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app('events')->listen(ManipulateAST::class, function (ManipulateAST $event) {

            $connectionInterface = PartialParser::interfaceTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
            """
            An interface that for `Connection` types. It is used for pagination in a `relay`-style.
            """
            interface Connection {
                """
                Information about the currenly shown page.
                """
                pageInfo: PageInfo!
                """
                The edges of this connection.
                """
                edges: [Edge]
            }
GRAPHQL);
            $edgeInterface = PartialParser::interfaceTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
            """
            An edge in a `Connection`. It points to a certain `Node` in the structure.
            """
            interface Edge {
                """
                Information about the currenly shown page.
                """
                node: Node
                """
                The cursor of the edge.
                """
                cursor: String!
            }
GRAPHQL);
            $types = collect($event->documentAST->types);

            $types->filter(function ($value, $key) {
                return str_ends_with($key, 'Connection');
            })->each(function ($value, $key) {
                $value->interfaces[] = PartialParser::namedType('Connection');
            });

            $types->filter(function ($value, $key) {
                return str_ends_with($key, 'Edge');
            })->each(function ($value, $key) {
                $value->interfaces[] = PartialParser::namedType('Edge');
            });

            $event->documentAST->setTypeDefinition($connectionInterface);
            $event->documentAST->setTypeDefinition($edgeInterface);
        });
    }
}
