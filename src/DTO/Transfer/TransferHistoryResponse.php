<?php

namespace espolin\Pay2House\DTO\Transfer;

use espolin\Pay2House\DTO\BaseResponse;

class TransferHistoryResponse extends BaseResponse
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
     * Проверяет есть ли переводы в ответе
     */
    public function hasTransactions(): bool
    {
        return !empty($this->transactions);
    }

    /**
     * Получает количество переводов
     */
    public function getTransactionsCount(): int
    {
        return count($this->transactions);
    }

    /**
     * Получает общее количество переводов (для пагинации)
     */
    public function getTotalCount(): int
    {
        return $this->count;
    }

    /**
     * Фильтрует переводы по статусу
     */
    public function filterByStatus(string $status): array
    {
        return array_filter($this->transactions, function ($transaction) use ($status) {
            return ($transaction['transaction_status'] ?? '') === $status;
        });
    }

    /**
     * Получает только подтвержденные переводы
     */
    public function getConfirmedTransactions(): array
    {
        return $this->filterByStatus('confirmed');
    }

    /**
     * Получает переводы в обработке
     */
    public function getProcessingTransactions(): array
    {
        return $this->filterByStatus('in_processing');
    }

    /**
     * Получает переводы с ошибками
     */
    public function getErrorTransactions(): array
    {
        return $this->filterByStatus('error');
    }

    /**
     * Вычисляет общую сумму переводов
     */
    public function getTotalAmount(): float
    {
        return array_reduce($this->transactions, function ($sum, $transaction) {
            return $sum + (float) ($transaction['amount'] ?? 0);
        }, 0.0);
    }

    /**
     * Вычисляет общую сумму комиссий
     */
    public function getTotalFees(): float
    {
        return array_reduce($this->transactions, function ($sum, $transaction) {
            return $sum + (float) ($transaction['fee_amount'] ?? 0);
        }, 0.0);
    }

    /**
     * Группирует переводы по дате
     */
    public function groupByDate(): array
    {
        $grouped = [];

        foreach ($this->transactions as $transaction) {
            $date = $transaction['date_created'] ?? 'unknown';
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $transaction;
        }

        return $grouped;
    }

    /**
     * Сортирует переводы по сумме
     */
    public function sortByAmount(bool $descending = true): array
    {
        $transactions = $this->transactions;

        usort($transactions, function ($a, $b) use ($descending) {
            $amountA = (float) ($a['amount'] ?? 0);
            $amountB = (float) ($b['amount'] ?? 0);

            return $descending ? $amountB <=> $amountA : $amountA <=> $amountB;
        });

        return $transactions;
    }

    /**
     * Получает статистику по переводам
     */
    public function getStatistics(): array
    {
        $confirmed = $this->getConfirmedTransactions();
        $processing = $this->getProcessingTransactions();
        $errors = $this->getErrorTransactions();

        return [
            'total_count' => $this->count,
            'returned_count' => $this->getTransactionsCount(),
            'confirmed_count' => count($confirmed),
            'processing_count' => count($processing),
            'error_count' => count($errors),
            'total_amount' => $this->getTotalAmount(),
            'total_fees' => $this->getTotalFees(),
            'average_amount' => $this->getTransactionsCount() > 0
                ? $this->getTotalAmount() / $this->getTransactionsCount()
                : 0
        ];
    }
}
