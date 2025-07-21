<?php

namespace App;

use Laravel\Lumen\Application;

class CustomLumenApp extends Application
{
    /**
     * Simpan callback untuk terminating.
     */
    protected $terminatingCallbacks = [];

    /**
     * Ambil fallback locale dari konfigurasi.
     */
    public function getFallbackLocale()
    {
        return $this['config']['app.fallback_locale'] ?? 'en';
    }

    /**
     * Daftarkan callback yang dijalankan saat aplikasi mengakhiri request.
     *
     * @param  \Closure|null  $callback
     * @return void
     */
    public function terminating(\Closure $callback = null)
    {
        if (is_null($callback)) {
            foreach ($this->terminatingCallbacks as $callback) {
                $callback();
            }
            return;
        }

        $this->terminatingCallbacks[] = $callback;
    }

    /**
     * Jalankan semua callback yang sudah didaftarkan.
     *
     * @return void
     */
    public function terminate()
    {
        $this->terminating();
    }
}
