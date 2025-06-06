<?php

namespace espolin\Pay2House\DTO\Transfer;

use espolin\Pay2House\DTO\BaseRequest;

class CreateTransferRequest extends BaseRequest
{
    public function __construct(
        public string $senderAccount,
        public string $recipientAccount,
        public float $amount,
        public string $comment = ''
    ) {}

    /**
     * Конвертирует в массив для API запроса
     * Убирает пустые комментарии и форматирует данные
     */
    public function toArray(): array
    {
        return array_filter([
            'sender_account' => $this->senderAccount,
            'recipient_account' => $this->recipientAccount,
            'amount' => $this->amount,
            'comment' => $this->comment ?: null,
        ], fn($value) => $value !== null);
    }

    /**
     * Валидирует данные перевода
     * Проверяет корректность всех обязательных параметров
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->senderAccount)) {
            $errors[] = 'Sender account is required';
        }

        if (empty($this->recipientAccount)) {
            $errors[] = 'Recipient account is required';
        }

        if ($this->senderAccount === $this->recipientAccount) {
            $errors[] = 'Sender and recipient accounts cannot be the same';
        }

        if ($this->amount <= 0) {
            $errors[] = 'Transfer amount must be greater than 0';
        }

        if ($this->amount > 1000000) {
            $errors[] = 'Transfer amount exceeds maximum limit';
        }

        if (strlen($this->comment) > 500) {
            $errors[] = 'Comment cannot exceed 500 characters';
        }

        // Проверяем формат номеров счетов (должны начинаться с P2U)
        if (!preg_match('/^P2U\d+$/', $this->senderAccount)) {
            $errors[] = 'Invalid sender account format (should start with P2U)';
        }

        if (!preg_match('/^P2U\d+$/', $this->recipientAccount)) {
            $errors[] = 'Invalid recipient account format (should start with P2U)';
        }

        return $errors;
    }

    /**
     * Устанавливает комментарий к переводу
     */
    public function withComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Форматирует сумму перевода
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, '.', '');
    }
}
