<?php

namespace App\Providers;

use App\Services\StaticData\StaticData;
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
use CommerceGuys\Intl\Currency\CurrencyRepository;
use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use CommerceGuys\Intl\Language\LanguageRepository;
use CommerceGuys\Intl\Language\LanguageRepositoryInterface;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepository;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberUtil;

class StaticDataServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // \Validator::extend('before_fields', 'App\Services\Validators\DateTimeValidator@validateBeforeFields');
        // \Validator::extend('before_or_equal_fields', 'App\Services\Validators\DateTimeValidator@validateBeforeOrEqualFields');
        // \Validator::extend('after_fields', 'App\Services\Validators\DateTimeValidator@validateAfterFields');
        // \Validator::extend('after_or_equal_fields', 'App\Services\Validators\DateTimeValidator@validateAfterOrEqualFields');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NumberFormatRepository::class, function() {
            return new NumberFormatRepository(config('app.locale'));
        });
        $this->app->bind(NumberFormatRepositoryInterface::class, NumberFormatRepository::class);

        $this->app->singleton(CurrencyRepository::class, function () {
            return new CurrencyRepository(config('app.locale'), config('app.fallback_locale'));
        });
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);

        $this->app->singleton(LanguageRepository::class, function () {
            return new LanguageRepository(config('app.locale'), config('app.fallback_locale'));
        });
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);


        $this->app->singleton(StaticData::class);
    }
}
