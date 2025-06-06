<?php

namespace espolin\Pay2House;

use Illuminate\Support\ServiceProvider;
use espolin\Pay2House\Services\PaymentService;
use espolin\Pay2House\Services\TransferService;
use espolin\Pay2House\Services\WalletService;
use espolin\Pay2House\Services\CardService;

class Pay2HouseServiceProvider extends ServiceProvider
{
    /**
     * Регистрирует сервисы в контейнере
     * Биндит все сервисы как синглтоны для повторного использования
     */
    public function register(): void
    {
        // Мерж конфигурации с дефолтными значениями
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pay2house.php',
            'pay2house'
        );

        // Регистрируем основной класс Pay2House
        $this->app->singleton(Pay2House::class, function ($app) {
            return new Pay2House(
                config('pay2house.api_key'),
                config('pay2house.base_url')
            );
        });

        // Регистрируем сервисы как синглтоны
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                config('pay2house.api_key'),
                config('pay2house.base_url')
            );
        });

        $this->app->singleton(TransferService::class, function ($app) {
            return new TransferService(
                config('pay2house.api_key'),
                config('pay2house.base_url')
            );
        });

        $this->app->singleton(WalletService::class, function ($app) {
            return new WalletService(
                config('pay2house.api_key'),
                config('pay2house.base_url')
            );
        });

        $this->app->singleton(CardService::class, function ($app) {
            return new CardService(
                config('pay2house.api_key'),
                config('pay2house.base_url')
            );
        });
    }

    /**
     * Публикует конфигурацию и выполняет bootstrap операции
     */
    public function boot(): void
    {
        // Публикуем конфигурационный файл
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/pay2house.php' => config_path('pay2house.php'),
            ], 'pay2house-config');

            // Публикуем миграции для webhook логов (опционально)
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'pay2house-migrations');
        }

        // Регистрируем маршруты для webhook'ов если они включены
        if (config('pay2house.webhook.enabled', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/webhook.php');
        }
    }

    /**
     * Получает список сервисов предоставляемых провайдером
     */
    public function provides(): array
    {
        return [
            Pay2House::class,
            PaymentService::class,
            TransferService::class,
            WalletService::class,
            CardService::class,
        ];
    }
}
