<?php

namespace App;

use Laravel\Lumen\Application;

class CustomLumenApp extends Application
{
    public function getFallbackLocale()
    {
        return $this['config']['app.fallback_locale'] ?? 'en';
    }
}
