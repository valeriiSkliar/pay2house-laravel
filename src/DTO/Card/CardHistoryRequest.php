<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;
use espolin\Pay2House\DTO\BaseResponse;

// CardHistoryRequest.php
class CardHistoryRequest extends BaseRequest
{
    public function __construct(
        public string $cardId,
        public int $perPage = 25,
        public int $page = 1
    ) {}

    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
            'per_page' => $this->perPage,
            'page' => $this->page,
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

        if ($this->perPage < 10 || $this->perPage > 100) {
            $errors[] = 'Per page value must be between 10 and 100';
        }

        if ($this->page < 1) {
            $errors[] = 'Page number must be positive integer';
        }

        return $errors;
    }
}
