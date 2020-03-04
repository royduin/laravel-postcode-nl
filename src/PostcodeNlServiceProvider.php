<?php

namespace Speelpenning\PostcodeNl;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Speelpenning\PostcodeNl\Http\PostcodeNlClient;
use Speelpenning\PostcodeNl\Services\AddressLookup;
use Speelpenning\PostcodeNl\Validators\AddressLookupValidator;

class PostcodeNlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            $this->getPathToConfigFile() => config_path('postcode-nl.php')
        ], 'config');

        if (Arr::get($this->app['config'], 'postcode-nl.enableRoutes', false) and ! $this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getPathToConfigFile(), 'postcode-nl');

        $this->app->singleton(AddressLookup::class, function ($app) {
            return new AddressLookup($app[AddressLookupValidator::class], $app[PostcodeNlClient::class]);
        });
    }

    protected function getPathToConfigFile()
    {
        return __DIR__ . '/../config/postcode-nl.php';
    }
}
