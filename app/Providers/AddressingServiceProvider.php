<?php

namespace App\Providers;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Formatter\FormatterInterface;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatter;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatterInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberUtil;

class AddressingServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('country_code', 'App\Services\Validators\PostalAddressValidator@validateCountryCode');
        \Validator::extend('postal_code', 'App\Services\Validators\PostalAddressValidator@validatePostalCode');
        \Validator::extendImplicit('address_field', 'App\Services\Validators\PostalAddressValidator@validateAddressField');

        // \Validator::extend('before_fields', 'App\Services\Validators\DateTimeValidator@validateBeforeFields');
        // \Validator::extend('before_or_equal_fields', 'App\Services\Validators\DateTimeValidator@validateBeforeOrEqualFields');
        // \Validator::extend('after_fields', 'App\Services\Validators\DateTimeValidator@validateAfterFields');
        // \Validator::extend('after_or_equal_fields', 'App\Services\Validators\DateTimeValidator@validateAfterOrEqualFields');


        \Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            $country_code_attr = str_replace('phone_number','country_code',$attribute);
            $country_code = \Arr::get($validator->getData(), $country_code_attr, 'NL');
            $util = resolve(PhoneNumberUtil::class);
            $phone_number = $util->parse($value, $country_code);
            return $util->isValidNumber($phone_number);
        });
    }

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


        // ADDRESSING
        // Repositories.
        $this->app->singleton(CountryRepository::class, function () {
            return new CountryRepository('nl', 'en');
        });
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->singleton(AddressFormatRepository::class);
        $this->app->bind(AddressFormatRepositoryInterface::class, AddressFormatRepository::class);
        $this->app->singleton(SubdivisionRepository::class);
        $this->app->bind(SubdivisionRepositoryInterface::class, SubdivisionRepository::class);
        // Formatters
        $this->app->singleton(DefaultFormatter::class, function (Container $app) {
            return new DefaultFormatter(
                $app->make(AddressFormatRepositoryInterface::class),
                $app->make(CountryRepositoryInterface::class),
                $app->make(SubdivisionRepositoryInterface::class),
                [
                    'locale' => 'nl',
                    'html' => false,
                    'html_tag' => 'div',
                ]
            );
        });
        $this->app->singleton(FormatterInterface::class, DefaultFormatter::class);
        $this->app->singleton(PostalLabelFormatter::class, function (Container $app) {
            return new PostalLabelFormatter(
                $app->make(AddressFormatRepositoryInterface::class),
                $app->make(CountryRepositoryInterface::class),
                $app->make(SubdivisionRepositoryInterface::class),
                [
                    'locale' => 'nl',
                    'html' => false,
                    'html_tag' => 'div',
                    'origin_country' => 'NL',
                ]
            );
        });
        $this->app->bind(PostalLabelFormatterInterface::class, PostalLabelFormatter::class);

        // VALIDATORS
        $this->app->singleton(\App\Services\Validators\PostalAddressValidator::class);
    }
}
