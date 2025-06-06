<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseResponse;

class GetCardsResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public int $count,
        public array $cards
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            count: (int) $data['count'],
            cards: $data['cards'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'count' => $this->count,
            'cards' => $this->cards,
        ];
    }

    /**
     * Проверяет есть ли карты в ответе
     */
    public function hasCards(): bool
    {
        return !empty($this->cards);
    }

    /**
     * Получает количество возвращенных карт
     */
    public function getCardsCount(): int
    {
        return count($this->cards);
    }

    /**
     * Получает общее количество карт (для пагинации)
     */
    public function getTotalCount(): int
    {
        return $this->count;
    }

    /**
     * Фильтрует карты по статусу
     */
    public function filterByStatus(string $status): array
    {
        return array_filter($this->cards, function ($card) use ($status) {
            return ($card['card_status'] ?? '') === $status;
        });
    }

    /**
     * Получает активные карты
     */
    public function getActiveCards(): array
    {
        return $this->filterByStatus('active');
    }

    /**
     * Получает заблокированные карты
     */
    public function getBlockedCards(): array
    {
        return $this->filterByStatus('blocked');
    }

    /**
     * Вычисляет общий баланс всех карт
     */
    public function getTotalBalance(): float
    {
        return array_reduce($this->cards, function ($sum, $card) {
            return $sum + (float) ($card['balance'] ?? 0);
        }, 0.0);
    }

    /**
     * Группирует карты по валютам
     */
    public function groupByCurrency(): array
    {
        $grouped = [];

        foreach ($this->cards as $card) {
            $currency = $card['currency_code'] ?? 'unknown';
            if (!isset($grouped[$currency])) {
                $grouped[$currency] = [];
            }
            $grouped[$currency][] = $card;
        }

        return $grouped;
    }

    /**
     * Получает статистику по картам
     */
    public function getStatistics(): array
    {
        $active = $this->getActiveCards();
        $blocked = $this->getBlockedCards();

        return [
            'total_count' => $this->count,
            'returned_count' => $this->getCardsCount(),
            'active_count' => count($active),
            'blocked_count' => count($blocked),
            'total_balance' => $this->getTotalBalance(),
            'currencies' => array_keys($this->groupByCurrency())
        ];
    }
}
