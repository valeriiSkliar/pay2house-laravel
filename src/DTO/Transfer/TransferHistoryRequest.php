<?php

namespace espolin\Pay2House\DTO\Transfer;

use espolin\Pay2House\DTO\BaseRequest;

class TransferHistoryRequest extends BaseRequest
{
    public function __construct(
        public int $perPage = 25,
        public int $page = 1,
        public ?string $transactionType = null,
        public ?string $status = null,
        public ?string $dateRange = null
    ) {}

    /**
     * Конвертирует в массив для API запроса
     * Убирает null значения и валидирует параметры
     */
    public function toArray(): array
    {
        return array_filter([
            'per_page' => $this->perPage,
            'page' => $this->page,
            'transaction_type' => $this->transactionType,
            'status' => $this->status,
            'date_range' => $this->dateRange,
        ], fn($value) => $value !== null);
    }

    /**
     * Валидирует параметры запроса истории
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->perPage < 10 || $this->perPage > 100) {
            $errors[] = 'Per page value must be between 10 and 100';
        }

        if ($this->page < 1) {
            $errors[] = 'Page number must be positive integer';
        }

        if ($this->transactionType && !in_array($this->transactionType, ['incoming', 'withdrawal'])) {
            $errors[] = 'Transaction type must be "incoming" or "withdrawal"';
        }

        if ($this->status && !in_array($this->status, ['in_processing', 'confirmed', 'error'])) {
            $errors[] = 'Status must be "in_processing", "confirmed", or "error"';
        }

        if ($this->dateRange && !$this->validateDateRange($this->dateRange)) {
            $errors[] = 'Date range must be in format "DD.MM.YYYY - DD.MM.YYYY"';
        }

        return $errors;
    }

    /**
     * Валидирует формат диапазона дат
     */
    private function validateDateRange(string $dateRange): bool
    {
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4} - \d{2}\.\d{2}\.\d{4}$/', $dateRange)) {
            return false;
        }

        $dates = explode(' - ', $dateRange);
        if (count($dates) !== 2) {
            return false;
        }

        try {
            $startDate = \DateTime::createFromFormat('d.m.Y', $dates[0]);
            $endDate = \DateTime::createFromFormat('d.m.Y', $dates[1]);

            return $startDate && $endDate && $startDate <= $endDate;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Устанавливает фильтр по типу транзакции
     */
    public function filterByType(string $type): self
    {
        $this->transactionType = $type;
        return $this;
    }

    /**
     * Устанавливает фильтр по статусу
     */
    public function filterByStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Устанавливает диапазон дат
     */
    public function filterByDateRange(string $startDate, string $endDate): self
    {
        $this->dateRange = "{$startDate} - {$endDate}";
        return $this;
    }

    /**
     * Устанавливает фильтр только для исходящих переводов
     */
    public function onlyOutgoing(): self
    {
        return $this->filterByType('withdrawal');
    }

    /**
     * Устанавливает фильтр только для входящих переводов
     */
    public function onlyIncoming(): self
    {
        return $this->filterByType('incoming');
    }

    /**
     * Устанавливает фильтр только для подтвержденных переводов
     */
    public function onlyConfirmed(): self
    {
        return $this->filterByStatus('confirmed');
    }

    /**
     * Устанавливает фильтр для переводов в обработке
     */
    public function onlyProcessing(): self
    {
        return $this->filterByStatus('in_processing');
    }

    /**
     * Создает запрос для последних переводов
     */
    public static function forRecent(int $limit = 25): self
    {
        return new self(perPage: min($limit, 100), page: 1);
    }

    /**
     * Создает запрос для переводов за последний месяц
     */
    public static function forLastMonth(): self
    {
        $endDate = date('d.m.Y');
        $startDate = date('d.m.Y', strtotime('-1 month'));

        return (new self())->filterByDateRange($startDate, $endDate);
    }
}
