<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseResponse;

class CardHistoryResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public int $count,
        public array $transactions
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            count: (int) $data['count'],
            transactions: $data['transactions'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'count' => $this->count,
            'transactions' => $this->transactions,
        ];
    }

    /**
     * Проверяет есть ли транзакции
     */
    public function hasTransactions(): bool
    {
        return !empty($this->transactions);
    }

    /**
     * Получает количество транзакций
     */
    public function getTransactionsCount(): int
    {
        return count($this->transactions);
    }

    /**
     * Фильтрует транзакции по типу
     */
    public function filterByType(string $type): array
    {
        return array_filter($this->transactions, function ($transaction) use ($type) {
            return ($transaction['type'] ?? '') === $type;
        });
    }

    /**
     * Получает только успешные транзакции
     */
    public function getSuccessfulTransactions(): array
    {
        return array_filter($this->transactions, function ($transaction) {
            return ($transaction['status'] ?? '') === 'success';
        });
    }

    /**
     * Вычисляет общую сумму транзакций
     */
    public function getTotalAmount(): float
    {
        return array_reduce($this->transactions, function ($sum, $transaction) {
            return $sum + (float) ($transaction['transaction_amount'] ?? 0);
        }, 0.0);
    }

    /**
     * Группирует транзакции по дате
     */
    public function groupByDate(): array
    {
        $grouped = [];

        foreach ($this->transactions as $transaction) {
            $date = date('Y-m-d', $transaction['transaction_time'] ?? time());
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $transaction;
        }

        return $grouped;
    }

    /**
     * Получает статистику по транзакциям
     */
    public function getStatistics(): array
    {
        $successful = $this->getSuccessfulTransactions();

        return [
            'total_count' => $this->count,
            'returned_count' => $this->getTransactionsCount(),
            'successful_count' => count($successful),
            'total_amount' => $this->getTotalAmount(),
            'average_amount' => $this->getTransactionsCount() > 0
                ? $this->getTotalAmount() / $this->getTransactionsCount()
                : 0
        ];
    }
}
