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
    }
}