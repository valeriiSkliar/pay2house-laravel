<?php

namespace espolin\Pay2House\Services;

use espolin\Pay2House\Client\BaseApiClient;
use espolin\Pay2House\DTO\Card\GetCardsRequest;
use espolin\Pay2House\DTO\Card\GetCardsResponse;
use espolin\Pay2House\DTO\Card\IssueCardRequest;
use espolin\Pay2House\DTO\Card\IssueCardResponse;
use espolin\Pay2House\DTO\Card\CardDetailsRequest;
use espolin\Pay2House\DTO\Card\CardDetailsResponse;
use espolin\Pay2House\DTO\Card\TopUpCardRequest;
use espolin\Pay2House\DTO\Card\TopUpCardResponse;
use espolin\Pay2House\DTO\Card\RefundCardRequest;
use espolin\Pay2House\DTO\Card\RefundCardResponse;
use espolin\Pay2House\DTO\Card\CardHistoryRequest;
use espolin\Pay2House\DTO\Card\CardHistoryResponse;
use espolin\Pay2House\DTO\Card\RefreshBalanceRequest;
use espolin\Pay2House\DTO\Card\ConfirmationCodesRequest;
use espolin\Pay2House\DTO\Card\BlockCardRequest;
use espolin\Pay2House\DTO\Card\CloseCardRequest;

class CardService extends BaseApiClient
{
    /**
     * Получает список виртуальных карт с фильтрацией
     * Позволяет фильтровать карты по статусу и использовать пагинацию
     */
    public function getCards(GetCardsRequest $request): GetCardsResponse
    {
        $data = $this->post('cards', $request->toArray());

        return GetCardsResponse::fromArray($data);
    }

    /**
     * Выпускает новую виртуальную карту
     * Создает карту с указанными персональными данными и привязывает к счету
     */
    public function issueCard(IssueCardRequest $request): IssueCardResponse
    {
        $data = $this->post('cards/issue', $request->toArray());

        return IssueCardResponse::fromArray($data);
    }

    /**
     * Получает детальную информацию о карте
     * Возвращает полную информацию включая номер, CVV, данные владельца
     */
    public function getCardDetails(CardDetailsRequest $request): CardDetailsResponse
    {
        $data = $this->post('cards/details', $request->toArray());

        return CardDetailsResponse::fromArray($data);
    }

    /**
     * Пополняет баланс виртуальной карты
     * Переводит средства с внутреннего счета на карту
     */
    public function topUpCard(TopUpCardRequest $request): TopUpCardResponse
    {
        $data = $this->post('cards/top_up', $request->toArray());

        return TopUpCardResponse::fromArray($data);
    }

    /**
     * Возвращает средства с карты на внутренний счет
     * Списывает указанную сумму с карты и переводит на связанный счет
     */
    public function refundCard(RefundCardRequest $request): RefundCardResponse
    {
        $data = $this->post('cards/refund', $request->toArray());

        return RefundCardResponse::fromArray($data);
    }

    /**
     * Получает историю операций по карте
     * Возвращает список всех транзакций с пагинацией
     */
    public function getCardHistory(CardHistoryRequest $request): CardHistoryResponse
    {
        $data = $this->post('cards/history', $request->toArray());

        return CardHistoryResponse::fromArray($data);
    }

    /**
     * Обновляет баланс карты
     * Синхронизирует баланс с внешней системой
     */
    public function refreshCardBalance(RefreshBalanceRequest $request): array
    {
        $data = $this->post('cards/balance/refresh', $request->toArray());

        return $data;
    }

    /**
     * Получает коды подтверждения для карты
     * Возвращает OTP коды необходимые для авторизации операций
     */
    public function getConfirmationCodes(ConfirmationCodesRequest $request): array
    {
        $data = $this->post('cards/confirmation_codes', $request->toArray());

        return $data['codes'] ?? [];
    }

    /**
     * Блокирует виртуальную карту
     * Карта становится недоступной для использования, средства возвращаются
     */
    public function blockCard(BlockCardRequest $request): array
    {
        $data = $this->post('cards/block', $request->toArray());

        return $data;
    }

    /**
     * Закрывает виртуальную карту
     * Окончательно закрывает карту (только при нулевом балансе)
     */
    public function closeCard(CloseCardRequest $request): array
    {
        $data = $this->post('cards/close', $request->toArray());

        return $data;
    }

    // ===================== УДОБНЫЕ МЕТОДЫ =====================

    /**
     * Получает все активные карты
     * Быстрый способ получить только действующие карты
     */
    public function getActiveCards(int $perPage = 25, int $page = 1): GetCardsResponse
    {
        $request = new GetCardsRequest(
            perPage: $perPage,
            page: $page,
            status: 'active'
        );

        return $this->getCards($request);
    }

    /**
     * Получает заблокированные карты
     */
    public function getBlockedCards(int $perPage = 25, int $page = 1): GetCardsResponse
    {
        $request = new GetCardsRequest(
            perPage: $perPage,
            page: $page,
            status: 'blocked'
        );

        return $this->getCards($request);
    }

    /**
     * Получает карты в ожидании выпуска
     */
    public function getPendingCards(int $perPage = 25, int $page = 1): GetCardsResponse
    {
        $request = new GetCardsRequest(
            perPage: $perPage,
            page: $page,
            status: 'awaiting'
        );

        return $this->getCards($request);
    }

    /**
     * Получает информацию о карте по ID
     */
    public function getCardById(string $cardId): CardDetailsResponse
    {
        $request = new CardDetailsRequest($cardId);
        return $this->getCardDetails($request);
    }

    /**
     * Простое пополнение карты
     */
    public function topUpCardSimple(string $cardId, string $accountNumber, float $amount): TopUpCardResponse
    {
        $request = new TopUpCardRequest($cardId, $accountNumber, $amount);
        return $this->topUpCard($request);
    }

    /**
     * Простой возврат с карты
     */
    public function refundCardSimple(string $cardId, float $amount): RefundCardResponse
    {
        $request = new RefundCardRequest($cardId, $amount);
        return $this->refundCard($request);
    }

    /**
     * Получает баланс карты
     */
    public function getCardBalance(string $cardId): float
    {
        try {
            $details = $this->getCardById($cardId);
            return $details->balance;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Проверяет является ли карта активной
     */
    public function isCardActive(string $cardId): bool
    {
        try {
            $details = $this->getCardById($cardId);
            return $details->isActive();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверяет заблокирована ли карта
     */
    public function isCardBlocked(string $cardId): bool
    {
        try {
            $details = $this->getCardById($cardId);
            return $details->isBlocked();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Получает последние операции по карте
     */
    public function getRecentCardTransactions(string $cardId, int $limit = 10): array
    {
        $request = new CardHistoryRequest(
            cardId: $cardId,
            perPage: min($limit, 100),
            page: 1
        );

        $history = $this->getCardHistory($request);
        return $history->transactions ?? [];
    }

    /**
     * Выпускает карту с минимальными данными
     */
    public function issueSimpleCard(
        string $accountNumber,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $address,
        string $city,
        string $countryCode,
        string $postCode,
        string $binNumber
    ): IssueCardResponse {
        $request = new IssueCardRequest(
            accountNumber: $accountNumber,
            firstName: $firstName,
            lastName: $lastName,
            surName: '', // Отчество опционально
            dateOfBirth: $dateOfBirth,
            phoneNumber: $phone,
            address: $address,
            city: $city,
            region: $city, // Используем город как регион по умолчанию
            postCode: $postCode,
            email: $email,
            countryCode: $countryCode,
            binNumber: $binNumber
        );

        return $this->issueCard($request);
    }

    /**
     * Обновляет и возвращает актуальный баланс карты
     */
    public function getRefreshedCardBalance(string $cardId): float
    {
        try {
            $request = new RefreshBalanceRequest($cardId);
            $result = $this->refreshCardBalance($request);
            return (float) ($result['balance'] ?? 0.0);
        } catch (\Exception $e) {
            // Если обновление не удалось, получаем баланс обычным способом
            return $this->getCardBalance($cardId);
        }
    }

    /**
     * Получает статистику по картам
     */
    public function getCardsStatistics(): array
    {
        try {
            $active = $this->getActiveCards(1, 1);
            $blocked = $this->getBlockedCards(1, 1);
            $pending = $this->getPendingCards(1, 1);

            return [
                'total_active' => $active->count,
                'total_blocked' => $blocked->count,
                'total_pending' => $pending->count,
                'total_cards' => $active->count + $blocked->count + $pending->count
            ];
        } catch (\Exception $e) {
            return [
                'total_active' => 0,
                'total_blocked' => 0,
                'total_pending' => 0,
                'total_cards' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Проверяет можно ли пополнить карту на указанную сумму
     */
    public function canTopUpCard(string $cardId, float $amount): bool
    {
        try {
            $details = $this->getCardById($cardId);

            if (!$details->isActive()) {
                return false;
            }

            // Проверяем лимиты пополнения
            if ($amount < $details->minRechargeAmount) {
                return false;
            }

            if ($amount > $details->maxRechargeAmount) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверяет можно ли сделать возврат с карты
     */
    public function canRefundCard(string $cardId, float $amount): bool
    {
        try {
            $details = $this->getCardById($cardId);

            if (!$details->isActive()) {
                return false;
            }

            // Проверяем достаточность средств
            if ($amount > $details->balance) {
                return false;
            }

            // Проверяем минимальную сумму возврата
            if ($amount < $details->refundMinAmount) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Блокирует карту по ID
     */
    public function blockCardById(string $cardId): bool
    {
        try {
            $request = new BlockCardRequest($cardId);
            $result = $this->blockCard($request);
            return $result['status'] === 'success';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Закрывает карту по ID
     */
    public function closeCardById(string $cardId): bool
    {
        try {
            $request = new CloseCardRequest($cardId);
            $result = $this->closeCard($request);
            return $result['status'] === 'success';
        } catch (\Exception $e) {
            return false;
        }
    }
}
