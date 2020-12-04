<?php


namespace App\Providers;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class CursorPaginationServiceProvider extends ServiceProvider
{
    public function boot() {
        Builder::macro('cursorPaginate', function ($limit, $cursor) {

        });
    }
}
