<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Phones
        $this->app->singleton(PhoneNumberUtil::class, function() {
            return PhoneNumberUtil::getInstance();
        });

        $this->app->singleton(PhoneNumberOfflineGeocoder::class, function() {
            return PhoneNumberOfflineGeocoder::getInstance();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            $country_code_attr = str_replace('phone_number','country_code',$attribute);
            $country_code = \Arr::get($validator->getData(), $country_code_attr, 'NL');
            $util = resolve(PhoneNumberUtil::class);
            $phone_number = $util->parse($value, $country_code);
            return $util->isValidNumber($phone_number);
        });
    }
}
