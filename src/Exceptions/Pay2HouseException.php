<?php

namespace espolin\Pay2House\Exceptions;

use Exception;

class Pay2HouseException extends Exception
{
    protected string $errorCode;

    public function __construct(string $message = '', string $errorCode = '', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    /**
     * Получает код ошибки API
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Проверяет является ли ошибка связанной с аутентификацией
     */
    public function isAuthenticationError(): bool
    {
        $authErrors = [
            'INVALID_TOKEN',
            'INVALID_API_KEY',
            'API_KEY_NOT_FOUND',
            'INACTIVE_API_KEY',
            'SIGNATURE_DECODING_FAILED',
            'INVALID_SIGNATURE_DATA',
            'EXPIRED_SIGNATURE',
            'MISMATCHED_SIGNATURE_ISSUER',
            'SIGNATURE_VERIFICATION_FAILED',
        ];

        return in_array($this->errorCode, $authErrors);
    }

    /**
     * Проверяет является ли ошибка связанной с недостатком средств
     */
    public function isInsufficientFundsError(): bool
    {
        $fundErrors = [
            'INSUFFICIENT_SENDER_BALANCE',
            'AMOUNT_EXCEEDS_SENDER_BALANCE',
            'INSUFFICIENT_BALANCE_AFTER_FEE',
            'INSUFFICIENT_BALANCE',
        ];

        return in_array($this->errorCode, $fundErrors);
    }

    /**
     * Проверяет является ли ошибка валидационной
     */
    public function isValidationError(): bool
    {
        $validationErrors = [
            'INVALID_EXTERNAL_NUMBER',
            'INVALID_AMOUNT',
            'INVALID_CURRENCY_CODE',
            'INVALID_DESCRIPTION',
            'INVALID_RETURN_URL',
            'INVALID_CANCEL_URL',
            'INVALID_PAYER_EMAIL',
            'INVALID_COMMENT',
            'INVALID_NAME_WALLET',
            'INVALID_PAYMENT_METHOD',
        ];

        return in_array($this->errorCode, $validationErrors);
    }
}
