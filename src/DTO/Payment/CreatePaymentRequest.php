<?php

namespace espolin\Pay2House\DTO\Payment;

use espolin\Pay2House\DTO\BaseRequest;

class CreatePaymentRequest extends BaseRequest
{
    public function __construct(
        public string $externalNumber,
        public float $amount,
        public string $currencyCode,
        public string $merchantId,
        public string $description,
        public int $deadlineSeconds,
        public string $returnUrl,
        public string $cancelUrl,
        public ?float $handlingFee = null,
        public ?string $payerEmail = null,
        public string $paymentMethod = 'ALL'
    ) {}

    /**
     * Конвертирует в массив для API запроса
     * Убирает null значения и применяет нужные форматирования
     */
    public function toArray(): array
    {
        return array_filter([
            'external_number' => $this->externalNumber,
            'amount' => $this->amount,
            'currency_code' => $this->currencyCode,
            'merchant_id' => $this->merchantId,
            'description' => $this->description,
            'deadline_seconds' => $this->deadlineSeconds,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'handling_fee' => $this->handlingFee,
            'payer_email' => $this->payerEmail,
            'payment_method' => $this->paymentMethod,
        ], fn($value) => $value !== null);
    }

    /**
     * Валидирует данные запроса
     * Проверяет корректность основных параметров
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->externalNumber)) {
            $errors[] = 'External number is required';
        }

        if ($this->amount <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }

        if (empty($this->currencyCode)) {
            $errors[] = 'Currency code is required';
        }

        if (empty($this->merchantId)) {
            $errors[] = 'Merchant ID is required';
        }

        if (empty($this->description)) {
            $errors[] = 'Description is required';
        }

        if ($this->deadlineSeconds < 60 || $this->deadlineSeconds > 86400) {
            $errors[] = 'Deadline seconds must be between 60 and 86400';
        }

        if (empty($this->returnUrl) || !filter_var($this->returnUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Return URL must be a valid URL';
        }

        if (empty($this->cancelUrl) || !filter_var($this->cancelUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Cancel URL must be a valid URL';
        }

        if ($this->payerEmail && !filter_var($this->payerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Payer email must be valid';
        }

        $allowedPaymentMethods = ['ALL', 'PAY2_HOUSE', 'USDT_TRC20', 'CARDS'];
        if (!in_array($this->paymentMethod, $allowedPaymentMethods)) {
            $errors[] = 'Payment method must be one of: ' . implode(', ', $allowedPaymentMethods);
        }

        return $errors;
    }
}
