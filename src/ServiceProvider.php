<?php

namespace Yabloncev\StreamTelecom;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        // publishes config
        $this->publishes([
            __DIR__ . '/../config/stream-telecom.php' => config_path('stream-telecom.php'),
        ]);
    }
}
