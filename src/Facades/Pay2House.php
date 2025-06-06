<?php

namespace espolin\Pay2House\Facades;

use Illuminate\Support\Facades\Facade;
use espolin\Pay2House\Services\PaymentService;
use espolin\Pay2House\Services\TransferService;
use espolin\Pay2House\Services\WalletService;
use espolin\Pay2House\Services\CardService;

/**
 * @method static PaymentService payments()
 * @method static TransferService transfers()
 * @method static WalletService wallets()
 * @method static CardService cards()
 * @method static bool ping()
 * @method static string getVersion()
 * @method static \espolin\Pay2House\Pay2House make(?string $apiKey = null, ?string $baseUrl = null)
 *
 * @see \espolin\Pay2House\Pay2House
 */
class Pay2House extends Facade
{
    /**
     * Получает зарегистрированное имя компонента в контейнере
     */
    protected static function getFacadeAccessor(): string
    {
        return \espolin\Pay2House\Pay2House::class;
    }
}
