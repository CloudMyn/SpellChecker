<?php

namespace CloudMyn\SpellChecker;

use CloudMyn\SpellChecker\Console\GenerateDictionary;
use Illuminate\Support\ServiceProvider;

class SpellCheckerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // publish configuration and migration
        // cmd: php artisan vendor:publish --provider="CloudMyn\SpellChecker\SpellCheckerServiceProvider" --tag="config"
        if ($this->app->runningInConsole()) {

            $this->commands([
                GenerateDictionary::class
            ]);

            // publish config file
            $this->publishes([
                __DIR__ . '/../config/spellchecker.php' => config_path('spellchecker.php'),
            ], 'config');

            // ...
        }
    }

    /**
     *  Call before anything setup
     */
    public function register()
    {
    }
}
