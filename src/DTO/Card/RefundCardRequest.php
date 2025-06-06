<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;

class RefundCardRequest extends BaseRequest
{
    public function __construct(
        public string $cardId,
        public float $amount
    ) {}

    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
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

        if ($this->amount <= 0) {
            $errors[] = 'Refund amount must be greater than 0';
        }

        return $errors;
    }
}
