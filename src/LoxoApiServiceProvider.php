<?php

namespace Loxo\LaravelApi;

use Illuminate\Support\ServiceProvider;
use Loxo\LaravelApi\Contracts\LoxoApiInterface;
use Loxo\LaravelApi\Services\LoxoApiService;

class LoxoApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/loxo.php',
            'loxo'
        );

        // Bind the service to the container
        $this->app->singleton(LoxoApiInterface::class, LoxoApiService::class);
        $this->app->singleton('loxo-api', LoxoApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/loxo.php' => config_path('loxo.php'),
            ], 'loxo-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            LoxoApiInterface::class,
            'loxo-api',
        ];
    }
}
