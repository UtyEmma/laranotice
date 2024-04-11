<?php

namespace Utyemma\LaraNotice;

use Illuminate\Support\ServiceProvider;
use Utyemma\LaraNotice\Commands\CreateMailable;

class LaraNoticeServiceProvider extends ServiceProvider {

    function boot(){
        $this->registerCommands();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2024_01_18_133341_create_mailables_table.php');

        $this->publishes([
            __DIR__.'/../config/laranotice.php' => config_path('laranotice.php'),
        ]);

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'laranotice-migrations');

        // $this->app->bind('notifire', function($app) {
        //     return new Notifire();
        // });
    }

    function register() {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laranotice.php', 'laranotice'
        );
    }

    function registerCommands(){
        if($this->app->runningInConsole()){
            $this->commands([
                CreateMailable::class
            ]);
        }
    }

}
