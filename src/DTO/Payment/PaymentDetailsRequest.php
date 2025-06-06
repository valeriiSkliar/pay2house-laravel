<?php

namespace espolin\Pay2House\DTO\Payment;

use espolin\Pay2House\DTO\BaseRequest;

class PaymentDetailsRequest extends BaseRequest
{
    public function __construct(
        public string $merchantId,
        public string $invoiceNumber
    ) {}

    public function toArray(): array
    {
        return [
            'merchant_id' => $this->merchantId,
            'invoice_number' => $this->invoiceNumber,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->merchantId)) {
            $errors[] = 'Merchant ID is required';
        }

        if (empty($this->invoiceNumber)) {
            $errors[] = 'Invoice number is required';
        }

        return $errors;
    }
}

// PaymentDetailsResponse.php
class PaymentDetailsResponse extends \espolin\Pay2House\DTO\BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $invoiceNumber,
        public string $currencyCode,
        public string $currencySymbol,
        public string $externalNumber,
        public string $description,
        public float $amount,
        public float $handlingFee,
        public string $paymentStatus
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            invoiceNumber: $data['invoice_number'],
            currencyCode: $data['currency_code'],
            currencySymbol: $data['currency_symbol'],
            externalNumber: $data['external_number'],
            description: $data['description'],
            amount: (float) $data['amount'],
            handlingFee: (float) $data['handling_fee'],
            paymentStatus: $data['payment_status']
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'invoice_number' => $this->invoiceNumber,
            'currency_code' => $this->currencyCode,
            'currency_symbol' => $this->currencySymbol,
            'external_number' => $this->externalNumber,
            'description' => $this->description,
            'amount' => $this->amount,
            'handling_fee' => $this->handlingFee,
            'payment_status' => $this->paymentStatus,
        ];
    }

    public function isPaid(): bool
    {
        return $this->paymentStatus === 'paid';
    }

    public function isPending(): bool
    {
        return $this->paymentStatus === 'pending';
    }

    public function isCancelled(): bool
    {
        return in_array($this->paymentStatus, ['cancelled', 'overdue']);
    }

    public function getTotalAmount(): float
    {
        return $this->amount + $this->handlingFee;
    }
}
