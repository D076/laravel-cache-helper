<?php

namespace D076\LaravelCacheHelper\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use RuntimeException;
use Throwable;

trait HasCached
{
    protected static function getDefaultTtl(): int
    {
        return config('cache.default_ttl', 60);
    }

    public function __call(string $name, array $arguments)
    {
        [$className, $originalName, $arguments, $cacheKey, $ttl] = self::handleMagicCall($name, $arguments);

        return Cache::remember($cacheKey, $ttl, function () use ($className, $originalName, $arguments) {
            return call_user_func_array([$className, $originalName], $arguments);
        });
    }

    public static function __callStatic(string $name, array $arguments)
    {
        [$className, $originalName, $arguments, $cacheKey, $ttl] = self::handleMagicCall($name, $arguments);

        return Cache::remember($cacheKey, $ttl, function () use ($className, $originalName, $arguments) {
            return call_user_func_array([$className, $originalName], $arguments);
        });
    }

    protected static function handleMagicCall(string $name, array $arguments): array
    {
        $forget = false;
        $originalName = $name;

        if (Str::endsWith($name, 'ForceCached')) {
            $originalName = Str::replace('ForceCached', '', $name);
            $forget = true;
        } elseif (Str::endsWith($name, 'Cached')) {
            $originalName = Str::replace('Cached', '', $name);
        }

        $className = self::class;

        try {
            $ref = new ReflectionClass($className);

            $method = $ref->getMethod($originalName);
        } catch (Throwable $e) {
            throw new RuntimeException("Method [$name] not found in [$className].");
        }

        $numParams = $method->getNumberOfParameters();

        $ttl = Arr::get($arguments, $numParams, self::getDefaultTtl());
        $ttl = is_numeric($ttl) ? (int)$ttl : self::getDefaultTtl();

        Arr::forget($arguments, $numParams);

        $cacheKeyComponents = [$className, $originalName, ...$arguments];

        $cacheKey = self::getCacheKey($cacheKeyComponents);

        if ($forget) {
            Cache::forget($cacheKey);
        }

        return [$className, $originalName, $arguments, $cacheKey, $ttl];
    }

    protected static function getCacheKey(array $cacheKeyComponents): string
    {
        foreach ($cacheKeyComponents as $key => $value) {
            if ($value instanceof Model) {
                $cacheKeyComponents[$key] = class_basename($value) . '-' . $value->getKey();
                continue;
            }

            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            if (is_array($value)) {
                $cacheKeyComponents[$key] = '[' . self::getCacheKey($value) . ']';
                continue;
            }

            if (is_object($value)) {
                $cacheKeyComponents[$key] = '{' . self::getCacheKey((array)$value) . '}';
            }
        }

        return implode(':', $cacheKeyComponents);
    }
}