<?php

namespace espolin\Pay2House\DTO\Payment;

use espolin\Pay2House\DTO\BaseResponse;

class CreatePaymentResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $invoiceNumber,
        public string $approvalUrl
    ) {}

    /**
     * Создает объект из ответа API
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            invoiceNumber: $data['invoice_number'],
            approvalUrl: $data['approval_url']
        );
    }

    /**
     * Проверяет успешность создания платежа
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success' && $this->code === 'REQUEST_SUCCESS';
    }

    /**
     * Получает URL для перенаправления пользователя на оплату
     */
    public function getPaymentUrl(): string
    {
        return $this->approvalUrl;
    }

    /**
     * Получает номер инвойса для дальнейшего отслеживания
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * Конвертирует в массив
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'invoice_number' => $this->invoiceNumber,
            'approval_url' => $this->approvalUrl,
        ];
    }
}
