<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;
use espolin\Pay2House\DTO\BaseResponse;

// CardDetailsRequest.php
class CardDetailsRequest extends BaseRequest
{
    public function __construct(
        public string $cardId
    ) {}

    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
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

        return $errors;
    }
}





// RefundCardRequest.php


// RefundCardResponse.php
