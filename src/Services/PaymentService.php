<?php

namespace espolin\Pay2House\Services;

use espolin\Pay2House\Client\BaseApiClient;
use espolin\Pay2House\DTO\Payment\CreatePaymentRequest;
use espolin\Pay2House\DTO\Payment\PaymentDetailsRequest;
use espolin\Pay2House\DTO\Payment\CreatePaymentResponse;
use espolin\Pay2House\DTO\Payment\PaymentDetailsResponse;

class PaymentService extends BaseApiClient
{
    /**
     * Создает новый платеж
     * Используется для инициализации платежа через Pay2.House
     */
    public function createPayment(CreatePaymentRequest $request): CreatePaymentResponse
    {
        $data = $this->post('create_payment', $request->toArray());

        return CreatePaymentResponse::fromArray($data);
    }

    /**
     * Получает информацию о платеже
     * Позволяет запросить статус и детали существующего платежа
     */
    public function getPaymentDetails(PaymentDetailsRequest $request): PaymentDetailsResponse
    {
        $data = $this->post('show_payment_details', $request->toArray());

        return PaymentDetailsResponse::fromArray($data);
    }

    /**
     * Создает платеж с упрощенными параметрами
     * Удобный метод для быстрого создания платежа с базовыми параметрами
     */
    public function createSimplePayment(
        string $externalNumber,
        float $amount,
        string $currencyCode,
        string $merchantId,
        string $description,
        string $returnUrl,
        string $cancelUrl,
        int $deadlineSeconds = 600
    ): CreatePaymentResponse {
        $request = new CreatePaymentRequest(
            externalNumber: $externalNumber,
            amount: $amount,
            currencyCode: $currencyCode,
            merchantId: $merchantId,
            description: $description,
            deadlineSeconds: $deadlineSeconds,
            returnUrl: $returnUrl,
            cancelUrl: $cancelUrl
        );

        return $this->createPayment($request);
    }

    /**
     * Проверяет статус платежа по номеру инвойса
     * Быстрый способ проверить оплачен ли платеж
     */
    public function isPaymentPaid(string $merchantId, string $invoiceNumber): bool
    {
        $request = new PaymentDetailsRequest($merchantId, $invoiceNumber);
        $details = $this->getPaymentDetails($request);

        return $details->paymentStatus === 'paid';
    }
}
