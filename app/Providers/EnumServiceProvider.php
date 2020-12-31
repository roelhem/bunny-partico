<?php

namespace App\Providers;

use Nuwave\Lighthouse\Schema\TypeRegistry;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;

class EnumServiceProvider extends ServiceProvider
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
     * @param TypeRegistry $typeRegistry
     */
    public function boot(TypeRegistry $typeRegistry)
    {
        foreach (config('enums.enums') as $key => $value) {
            $name = is_string($key) ? $key : null;
            $typeRegistry->register(new LaravelEnumType($value, $name));
        }
    }
}
