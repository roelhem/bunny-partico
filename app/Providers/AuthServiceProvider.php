<?php

namespace App\Providers;

use App\Contracts\AccessControl;
use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // An admin user can do anything.
        \Gate::before(function (?User $user) {
            if($user !== null && $user->is_admin) {
                return Response::allow('You are admin, you can do anything!');
            } else {
                return null;
            }
        });

        // Ensure that the abilities also conform with the access control.
        \Gate::after(function (?User $user, $ability, $result, $arguments) {
            if(count($arguments) > 0 && $arguments[0] instanceof AccessControl) {
                /** @var AccessControl $instance */
                $instance = $arguments[0];
                if(!$instance->hasAccess($ability, $user)) {
                    $currentAccessLevel = $instance->getAccess($user)->key;
                    return Response::deny("You don't have '$ability'-access as a '$currentAccessLevel'.");
                }

                if ($result === null) {
                    return Response::allow();
                }
            }
            return $result;
        });

        Passport::routes();
    }
}
