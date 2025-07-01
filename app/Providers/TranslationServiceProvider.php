<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Translation\Loader;
use App\Translation\TranslationLoader;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Loader::class, function ($app) {
            return new FileLoader(new Filesystem, $app['path.lang']);
        });
    
        // Bind your custom Translator
        $this->app->singleton('translator', function ($app) {
            $loader = $app[Loader::class];
            $locale = $app['config']['app.locale'];
    
            return new TranslationLoader($loader, $locale);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
