<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseResponse;

class TopUpCardResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $transactionNumber
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            transactionNumber: $data['transaction_number']
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'transaction_number' => $this->transactionNumber,
        ];
    }

    /**
     * Получает номер транзакции пополнения
     */
    public function getTransactionNumber(): string
    {
        return $this->transactionNumber;
    }
}
