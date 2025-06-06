<?php

namespace espolin\Pay2House\Services;

use espolin\Pay2House\Client\BaseApiClient;
use espolin\Pay2House\DTO\Wallet\GetWalletsRequest;
use espolin\Pay2House\DTO\Wallet\CreateWalletRequest;
use espolin\Pay2House\DTO\Wallet\WalletDetailsRequest;
use espolin\Pay2House\DTO\Wallet\WalletStatementRequest;

class WalletService extends BaseApiClient
{
    /**
     * Получает список всех кошельков
     * Позволяет фильтровать по валюте и статусу удаления
     */
    public function getWallets(?int $deleteFlag = 0, ?string $currencyCode = null): array
    {
        $request = new GetWalletsRequest($deleteFlag, $currencyCode);
        $data = $this->post('wallets', $request->toArray());

        return $data['wallets'] ?? [];
    }

    /**
     * Создает новый кошелек
     * Позволяет создать кошелек с указанным именем и валютой
     */
    public function createWallet(string $name, string $currencyCode): array
    {
        $request = new CreateWalletRequest($name, $currencyCode);
        $data = $this->post('wallets/create', $request->toArray());

        return $data;
    }

    /**
     * Получает детальную информацию о кошельке
     * Возвращает баланс, адреса и другую информацию
     */
    public function getWalletDetails(string $accountNumber): array
    {
        $request = new WalletDetailsRequest($accountNumber);
        $data = $this->post('wallets/details', $request->toArray());

        return $data;
    }

    /**
     * Получает выписку по кошельку
     * Возвращает URL для скачивания CSV файла с историей операций
     */
    public function getWalletStatement(string $accountNumber): string
    {
        $request = new WalletStatementRequest($accountNumber);
        $data = $this->post('wallets/statement', $request->toArray());

        return $data['download_url'] ?? '';
    }

    /**
     * Получает кошельки по валюте
     * Удобный метод для фильтрации кошельков по валюте
     */
    public function getWalletsByCurrency(string $currencyCode): array
    {
        return $this->getWallets(0, $currencyCode);
    }

    /**
     * Получает только активные кошельки
     */
    public function getActiveWallets(): array
    {
        return $this->getWallets(0);
    }

    /**
     * Проверяет существование кошелька
     */
    public function walletExists(string $accountNumber): bool
    {
        try {
            $this->getWalletDetails($accountNumber);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Получает баланс кошелька
     */
    public function getWalletBalance(string $accountNumber): float
    {
        $details = $this->getWalletDetails($accountNumber);
        return (float) ($details['balance'] ?? 0);
    }
}
