<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MensajeRespuestaHttpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        require_once app_path().'/Helpers/MensajesRespuesta.php';
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
