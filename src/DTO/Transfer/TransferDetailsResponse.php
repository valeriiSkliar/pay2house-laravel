<?php

namespace espolin\Pay2House\DTO\Transfer;

use espolin\Pay2House\DTO\BaseResponse;

class TransferDetailsResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $transactionNumber,
        public int $timeCreated,
        public string $dateCreated,
        public string $senderAccount,
        public string $recipientAccount,
        public float $amount,
        public float $feeAmount,
        public string $currencyCode,
        public string $paymentMethod,
        public string $confirmMethod,
        public string $paymentType,
        public string $transactionType,
        public string $transactionStatus,
        public string $comment = '',
        public string $errorMessage = ''
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            transactionNumber: $data['transaction_number'],
            timeCreated: (int) $data['time_created'],
            dateCreated: $data['date_created'],
            senderAccount: $data['sender_account'],
            recipientAccount: $data['recipient_account'],
            amount: (float) $data['amount'],
            feeAmount: (float) $data['fee_amount'],
            currencyCode: $data['currency_code'],
            paymentMethod: $data['payment_method'],
            confirmMethod: $data['confirm_method'],
            paymentType: $data['payment_type'],
            transactionType: $data['transaction_type'],
            transactionStatus: $data['transaction_status'],
            comment: $data['comment'] ?? '',
            errorMessage: $data['error_message'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'transaction_number' => $this->transactionNumber,
            'time_created' => $this->timeCreated,
            'date_created' => $this->dateCreated,
            'sender_account' => $this->senderAccount,
            'recipient_account' => $this->recipientAccount,
            'amount' => $this->amount,
            'fee_amount' => $this->feeAmount,
            'currency_code' => $this->currencyCode,
            'payment_method' => $this->paymentMethod,
            'confirm_method' => $this->confirmMethod,
            'payment_type' => $this->paymentType,
            'transaction_type' => $this->transactionType,
            'transaction_status' => $this->transactionStatus,
            'comment' => $this->comment,
            'error_message' => $this->errorMessage,
        ];
    }

    /**
     * Проверяет подтверждение перевода
     */
    public function isConfirmed(): bool
    {
        return $this->transactionStatus === 'confirmed';
    }

    /**
     * Проверяет обработку перевода
     */
    public function isProcessing(): bool
    {
        return $this->transactionStatus === 'in_processing';
    }

    /**
     * Проверяет ошибку перевода
     */
    public function hasError(): bool
    {
        return $this->transactionStatus === 'error';
    }

    /**
     * Проверяет является ли перевод исходящим
     */
    public function isOutgoing(): bool
    {
        return $this->transactionType === 'withdrawal';
    }

    /**
     * Проверяет является ли перевод входящим
     */
    public function isIncoming(): bool
    {
        return $this->transactionType === 'incoming';
    }

    /**
     * Получает итоговую сумму с комиссией
     */
    public function getTotalAmount(): float
    {
        return $this->amount + $this->feeAmount;
    }

    /**
     * Получает дату создания как объект Carbon
     */
    public function getCreatedAt(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromTimestamp($this->timeCreated);
    }

    /**
     * Получает статус перевода на русском языке
     */
    public function getStatusInRussian(): string
    {
        return match ($this->transactionStatus) {
            'in_processing' => 'Обрабатывается',
            'confirmed' => 'Подтвержден',
            'error' => 'Ошибка',
            default => 'Неизвестный статус'
        };
    }
}
