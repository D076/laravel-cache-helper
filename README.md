# laravel-cache-helper

<p align="left">
    <a href="https://laravel.com"><img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://php.net"><img alt="PHP 8.2" src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php"></a>
</p>

Small helper for Laravel Cache facade

## Quick Setup

```bash
composer require d076/laravel-cache-helper
```

## Usage

Add HasCached trait to your class which methods you want to cache.

```php
use D076\LaravelCacheHelper\Traits\HasCached;

class SomeClass
{
    use HasCached;
    
    public function someMethod($params)
    {
        ...
    }
    
    public static function someStaticMethod($params)
    {
        ...
    }
}
```

### Important
Your class or parent classes/traits should not contain override __call and __callStatic methods. 
So you can`t use HasCached trait in your Models.

Now you can call your methods with Cached or ForceCached prefix. 

Optionally, you can pass last parameter $ttl in seconds.

```php
$someClass->someMethodCached($params, $ttl);
$someClass->someMethodForceCached($params, $ttl);

SomeClass::someStaticMethodCached($params, $ttl);
SomeClass::someStaticMethodForceCached($params, $ttl);
```
