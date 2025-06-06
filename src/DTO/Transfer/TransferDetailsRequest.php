<?php

namespace espolin\Pay2House\DTO\Transfer;

use espolin\Pay2House\DTO\BaseRequest;

class TransferDetailsRequest extends BaseRequest
{
    public function __construct(
        public string $transactionNumber
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_number' => $this->transactionNumber,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->transactionNumber)) {
            $errors[] = 'Transaction number is required';
        }

        // Проверяем формат номера транзакции (должен начинаться с TN)
        if (!preg_match('/^TN\d+$/', $this->transactionNumber)) {
            $errors[] = 'Invalid transaction number format (should start with TN)';
        }

        return $errors;
    }
}
