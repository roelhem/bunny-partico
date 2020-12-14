<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class DashboardPassportSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Passport::setDefaultScope([
            'email',
            'profile',
            'address',
            'phone',
            'admin-access',
            'view-contacts',
            'view-contact-email-addresses',
            'view-contact-phone-numbers',
            'view-contact-postal-addresses',
        ]);

        return $next($request);
    }
}
