<?php

namespace YCMS\Providers;

use Illuminate\Support\ServiceProvider;
use YCMS\Extensions\DouyasiBlade;
use View;
use Illuminate\Support\MessageBag;
use YCMS\Facades\YCMS;
use YCMS\Extensions\BladeExtend;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        DouyasiBlade::register();
        BladeExtend::register();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


        if (class_exists('\Corcel\Database')) {
            \Corcel\Database::connect([
                'host'     => getenv('DB_HOST'),
                'database' => getenv('DB_DATABASE'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'prefix'   => 'wp_',
            ]);
        }

        View::share('errors', new MessageBag([]));
       //
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }
}
