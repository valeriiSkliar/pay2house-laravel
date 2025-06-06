<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;

class TopUpCardRequest extends BaseRequest
{
    public function __construct(
        public string $cardId,
        public string $accountNumber,
        public float $amount
    ) {}

    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
            'account_number' => $this->accountNumber,
            'amount' => $this->amount,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->cardId)) {
            $errors[] = 'Card ID is required';
        } elseif (!preg_match('/^VC\d+$/', $this->cardId)) {
            $errors[] = 'Invalid card ID format (should start with VC)';
        }

        if (empty($this->accountNumber)) {
            $errors[] = 'Account number is required';
        } elseif (!preg_match('/^P2U\d+$/', $this->accountNumber)) {
            $errors[] = 'Invalid account number format (should start with P2U)';
        }

        if ($this->amount <= 0) {
            $errors[] = 'Top-up amount must be greater than 0';
        }

        if ($this->amount > 100000) {
            $errors[] = 'Top-up amount exceeds maximum limit';
        }

        return $errors;
    }

    /**
     * Получает отформатированную сумму
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, '.', '');
    }
}
