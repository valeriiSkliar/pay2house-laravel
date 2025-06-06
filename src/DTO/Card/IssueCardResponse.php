<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseResponse;

class IssueCardResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $cardNumber,
        public string $cardId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            cardNumber: $data['card_number'],
            cardId: $data['card_id']
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'card_number' => $this->cardNumber,
            'card_id' => $this->cardId,
        ];
    }

    /**
     * Проверяет успешность выпуска карты
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success' && $this->code === 'REQUEST_SUCCESS';
    }

    /**
     * Получает номер выпущенной карты
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * Получает ID карты для дальнейших операций
     */
    public function getCardId(): string
    {
        return $this->cardId;
    }

    /**
     * Получает маскированный номер карты
     */
    public function getMaskedCardNumber(): string
    {
        if (strlen($this->cardNumber) >= 4) {
            return '**** **** **** ' . substr($this->cardNumber, -4);
        }

        return $this->cardNumber;
    }
}
