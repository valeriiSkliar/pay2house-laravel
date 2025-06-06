<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;

class GetCardsRequest extends BaseRequest
{
    public function __construct(
        public int $perPage = 25,
        public int $page = 1,
        public ?string $status = null
    ) {}

    /**
     * Конвертирует в массив для API запроса
     */
    public function toArray(): array
    {
        return array_filter([
            'per_page' => $this->perPage,
            'page' => $this->page,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }

    /**
     * Валидирует параметры запроса списка карт
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

        if ($this->status && !in_array($this->status, ['active', 'blocked', 'closed', 'awaiting'])) {
            $errors[] = 'Status must be one of: active, blocked, closed, awaiting';
        }

        return $errors;
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
     * Создает запрос для активных карт
     */
    public static function forActiveCards(int $perPage = 25, int $page = 1): self
    {
        return new self($perPage, $page, 'active');
    }

    /**
     * Создает запрос для заблокированных карт
     */
    public static function forBlockedCards(int $perPage = 25, int $page = 1): self
    {
        return new self($perPage, $page, 'blocked');
    }
}
