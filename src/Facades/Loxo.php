<?php

namespace Loxo\LaravelApi\Facades;

use Illuminate\Support\Facades\Facade;
use Loxo\LaravelApi\Contracts\LoxoApiInterface;

/**
 * @method static array getActivityTypes(array $params = [])
 * @method static array getAddressTypes(array $params = [])
 * @method static array get(string $endpoint, array $params = [])
 * @method static array post(string $endpoint, array $data = [])
 * @method static array put(string $endpoint, array $data = [])
 * @method static array delete(string $endpoint)
 * @method static string getBaseUrl()
 * @method static string getAgencySlug()
 * @method static string getDomain()
 *
 * @see \Loxo\LaravelApi\Services\LoxoApiService
 */
class Loxo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return LoxoApiInterface::class;
    }
}
