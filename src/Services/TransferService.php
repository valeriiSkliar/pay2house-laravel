<?php

namespace espolin\Pay2House\Services;

use espolin\Pay2House\Client\BaseApiClient;
use espolin\Pay2House\DTO\Transfer\CreateTransferRequest;
use espolin\Pay2House\DTO\Transfer\CreateTransferResponse;
use espolin\Pay2House\DTO\Transfer\TransferDetailsRequest;
use espolin\Pay2House\DTO\Transfer\TransferDetailsResponse;
use espolin\Pay2House\DTO\Transfer\TransferHistoryRequest;
use espolin\Pay2House\DTO\Transfer\TransferHistoryResponse;

class TransferService extends BaseApiClient
{
    /**
     * Создает новый внутренний перевод
     * Позволяет переводить средства между внутренними счетами в системе Pay2.House
     */
    public function createTransfer(CreateTransferRequest $request): CreateTransferResponse
    {
        $data = $this->post('transfers/create', $request->toArray());

        return CreateTransferResponse::fromArray($data);
    }

    /**
     * Получает детальную информацию о переводе
     * Возвращает полную информацию о переводе включая статус и комиссии
     */
    public function getTransferDetails(TransferDetailsRequest $request): TransferDetailsResponse
    {
        $data = $this->post('transfers/details', $request->toArray());

        return TransferDetailsResponse::fromArray($data);
    }

    /**
     * Получает историю переводов с фильтрацией
     * Позволяет получить список переводов с пагинацией и фильтрами
     */
    public function getTransferHistory(TransferHistoryRequest $request): TransferHistoryResponse
    {
        $data = $this->post('transfers', $request->toArray());

        return TransferHistoryResponse::fromArray($data);
    }

    /**
     * Создает простой перевод с базовыми параметрами
     * Удобный метод для быстрого создания перевода без создания DTO объекта
     */
    public function createSimpleTransfer(
        string $senderAccount,
        string $recipientAccount,
        float $amount,
        string $comment = ''
    ): CreateTransferResponse {
        $request = new CreateTransferRequest(
            senderAccount: $senderAccount,
            recipientAccount: $recipientAccount,
            amount: $amount,
            comment: $comment
        );

        return $this->createTransfer($request);
    }

    /**
     * Получает информацию о переводе по номеру транзакции
     * Упрощенный метод для получения деталей перевода
     */
    public function getTransferByNumber(string $transactionNumber): TransferDetailsResponse
    {
        $request = new TransferDetailsRequest($transactionNumber);
        return $this->getTransferDetails($request);
    }

    /**
     * Проверяет статус перевода
     * Возвращает true если перевод подтвержден
     */
    public function isTransferConfirmed(string $transactionNumber): bool
    {
        try {
            $details = $this->getTransferByNumber($transactionNumber);
            return $details->isConfirmed();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Получает последние переводы
     * Возвращает указанное количество последних переводов
     */
    public function getRecentTransfers(int $limit = 10): array
    {
        $request = new TransferHistoryRequest(
            perPage: min($limit, 100), // API ограничение
            page: 1
        );

        $response = $this->getTransferHistory($request);
        return $response->transactions;
    }

    /**
     * Получает исходящие переводы
     * Фильтрует переводы только по исходящим (withdrawal)
     */
    public function getOutgoingTransfers(int $perPage = 25, int $page = 1): TransferHistoryResponse
    {
        $request = new TransferHistoryRequest(
            perPage: $perPage,
            page: $page,
            transactionType: 'withdrawal'
        );

        return $this->getTransferHistory($request);
    }

    /**
     * Получает входящие переводы
     * Фильтрует переводы только по входящим (incoming)
     */
    public function getIncomingTransfers(int $perPage = 25, int $page = 1): TransferHistoryResponse
    {
        $request = new TransferHistoryRequest(
            perPage: $perPage,
            page: $page,
            transactionType: 'incoming'
        );

        return $this->getTransferHistory($request);
    }

    /**
     * Получает переводы только с определенным статусом
     * Удобный метод для фильтрации по статусу
     */
    public function getTransfersByStatus(
        string $status,
        int $perPage = 25,
        int $page = 1
    ): TransferHistoryResponse {
        $request = new TransferHistoryRequest(
            perPage: $perPage,
            page: $page,
            status: $status
        );

        return $this->getTransferHistory($request);
    }

    /**
     * Получает переводы за определенный период
     * Позволяет фильтровать переводы по датам
     */
    public function getTransfersByDateRange(
        string $startDate,
        string $endDate,
        int $perPage = 25,
        int $page = 1
    ): TransferHistoryResponse {
        $dateRange = "{$startDate} - {$endDate}";

        $request = new TransferHistoryRequest(
            perPage: $perPage,
            page: $page,
            dateRange: $dateRange
        );

        return $this->getTransferHistory($request);
    }

    /**
     * Проверяет возможность выполнения перевода
     * Валидирует параметры перевода без его создания
     */
    public function validateTransfer(
        string $senderAccount,
        string $recipientAccount,
        float $amount
    ): array {
        $request = new CreateTransferRequest(
            senderAccount: $senderAccount,
            recipientAccount: $recipientAccount,
            amount: $amount,
            comment: 'Validation check'
        );

        return $request->validate();
    }

    /**
     * Получает статистику переводов
     * Возвращает количество переводов по статусам
     */
    public function getTransferStats(): array
    {
        try {
            // Получаем первую страницу для анализа
            $confirmed = $this->getTransfersByStatus('confirmed', 1, 1);
            $processing = $this->getTransfersByStatus('in_processing', 1, 1);
            $errors = $this->getTransfersByStatus('error', 1, 1);

            return [
                'confirmed_count' => $confirmed->count,
                'processing_count' => $processing->count,
                'error_count' => $errors->count,
                'total_count' => $confirmed->count + $processing->count + $errors->count
            ];
        } catch (\Exception $e) {
            return [
                'confirmed_count' => 0,
                'processing_count' => 0,
                'error_count' => 0,
                'total_count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
