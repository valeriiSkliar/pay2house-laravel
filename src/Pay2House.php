<?php

namespace espolin\Pay2House;

use espolin\Pay2House\Services\PaymentService;
use espolin\Pay2House\Services\TransferService;
use espolin\Pay2House\Services\WalletService;
use espolin\Pay2House\Services\CardService;

class Pay2House
{
    protected PaymentService $paymentService;
    protected TransferService $transferService;
    protected WalletService $walletService;
    protected CardService $cardService;

    public function __construct(string $apiKey, string $baseUrl = 'https://pay2.house/api')
    {
        $this->paymentService = new PaymentService($apiKey, $baseUrl);
        $this->transferService = new TransferService($apiKey, $baseUrl);
        $this->walletService = new WalletService($apiKey, $baseUrl);
        $this->cardService = new CardService($apiKey, $baseUrl);
    }

    /**
     * Получает сервис для работы с платежами
     * Позволяет создавать платежи и получать информацию о них
     */
    public function payments(): PaymentService
    {
        return $this->paymentService;
    }

    /**
     * Получает сервис для работы с переводами
     * Позволяет создавать внутренние переводы и получать их историю
     */
    public function transfers(): TransferService
    {
        return $this->transferService;
    }

    /**
     * Получает сервис для работы с кошельками
     * Позволяет создавать кошельки, получать баланс и выписки
     */
    public function wallets(): WalletService
    {
        return $this->walletService;
    }

    /**
     * Получает сервис для работы с картами
     * Позволяет выпускать виртуальные карты и управлять ими
     */
    public function cards(): CardService
    {
        return $this->cardService;
    }

    /**
     * Статический метод для быстрого создания экземпляра
     */
    public static function make(?string $apiKey = null, ?string $baseUrl = null): self
    {
        return new self(
            $apiKey ?? config('pay2house.api_key'),
            $baseUrl ?? config('pay2house.base_url', 'https://pay2.house/api')
        );
    }

    /**
     * Проверяет доступность API
     * Полезно для health-check и диагностики
     */
    public function ping(): bool
    {
        try {
            // Попытка создать простой запрос для проверки соединения
            $this->wallets()->getWallets();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Получает информацию о версии библиотеки
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }
}
