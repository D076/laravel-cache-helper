<?php

namespace D076\LaravelCacheHelper;

class CacheHelperServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cache.php', 'cache'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/cache.php' => config_path('cache.php'),
        ]);
    }
}