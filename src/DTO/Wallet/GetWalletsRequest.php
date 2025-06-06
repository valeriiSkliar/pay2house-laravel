<?php

namespace espolin\Pay2House\DTO\Wallet;

use espolin\Pay2House\DTO\BaseRequest;

class GetWalletsRequest extends BaseRequest
{
    public function __construct(
        public ?int $deleteFlag = 0,
        public ?string $currencyCode = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'delete_flag' => $this->deleteFlag,
            'currency_code' => $this->currencyCode,
        ], fn($value) => $value !== null);
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->deleteFlag !== null && !in_array($this->deleteFlag, [0, 1])) {
            $errors[] = 'Delete flag must be 0 or 1';
        }

        if ($this->currencyCode && !in_array($this->currencyCode, ['USD', 'EUR', 'USDT'])) {
            $errors[] = 'Currency code must be USD, EUR, or USDT';
        }

        return $errors;
    }
}

// CreateWalletRequest.php
class CreateWalletRequest extends BaseRequest
{
    public function __construct(
        public string $name,
        public string $currencyCode
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'currency_code' => $this->currencyCode,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->name) || strlen($this->name) < 3) {
            $errors[] = 'Wallet name must be at least 3 characters long';
        }

        if (!in_array($this->currencyCode, ['USD', 'EUR', 'USDT'])) {
            $errors[] = 'Currency code must be USD, EUR, or USDT';
        }

        return $errors;
    }
}

// WalletDetailsRequest.php  
class WalletDetailsRequest extends BaseRequest
{
    public function __construct(
        public string $accountNumber
    ) {}

    public function toArray(): array
    {
        return [
            'account_number' => $this->accountNumber,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->accountNumber)) {
            $errors[] = 'Account number is required';
        }

        return $errors;
    }
}

// WalletStatementRequest.php
class WalletStatementRequest extends BaseRequest
{
    public function __construct(
        public string $accountNumber
    ) {}

    public function toArray(): array
    {
        return [
            'account_number' => $this->accountNumber,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->accountNumber)) {
            $errors[] = 'Account number is required';
        }

        return $errors;
    }
}
