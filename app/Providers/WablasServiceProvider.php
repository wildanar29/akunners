<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\WablasService;

class WablasServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(WablasService::class, function ($app) {
            return new WablasService('a869qeQFHi7r6vfThDBggM2xvG4pE97DuS5fTMFAAA53hr2JhwbFUPN8rgYg877B.0st8ewQU'); // Ganti dengan key yang benar
        });
    }

    public function boot()
    {
        //
    }
}
