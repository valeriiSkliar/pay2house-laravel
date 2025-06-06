<?php

namespace espolin\Pay2House\Helpers;

use espolin\Pay2House\DTO\Card\IssueCardRequest;

class CardBuilder
{
    private array $data = [];

    /**
     * Устанавливает номер счета
     */
    public function fromAccount(string $accountNumber): self
    {
        $this->data['account_number'] = $accountNumber;
        return $this;
    }

    /**
     * Устанавливает персональные данные
     */
    public function withPersonalInfo(
        string $firstName,
        string $lastName,
        string $dateOfBirth,
        string $surName = ''
    ): self {
        $this->data['first_name'] = $firstName;
        $this->data['last_name'] = $lastName;
        $this->data['sur_name'] = $surName;
        $this->data['date_birth'] = $dateOfBirth;
        return $this;
    }

    /**
     * Устанавливает контактную информацию
     */
    public function withContactInfo(string $email, string $phone): self
    {
        $this->data['email'] = $email;
        $this->data['phone_number'] = $phone;
        return $this;
    }

    /**
     * Устанавливает адрес
     */
    public function withAddress(
        string $address,
        string $city,
        string $countryCode,
        string $postCode,
        string $region = null
    ): self {
        $this->data['address'] = $address;
        $this->data['city'] = $city;
        $this->data['region'] = $region ?? $city;
        $this->data['country_code'] = $countryCode;
        $this->data['post_code'] = $postCode;
        return $this;
    }

    /**
     * Устанавливает BIN номер
     */
    public function withBinNumber(string $binNumber): self
    {
        $this->data['bin_number'] = $binNumber;
        return $this;
    }

    /**
     * Включает автоматическое продление
     */
    public function withAutoRenewal(bool $enabled = true): self
    {
        $this->data['renewal_payment_flag'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Создает запрос на выпуск карты
     */
    public function build(): IssueCardRequest
    {
        return new IssueCardRequest(
            accountNumber: $this->data['account_number'] ?? '',
            firstName: $this->data['first_name'] ?? '',
            lastName: $this->data['last_name'] ?? '',
            surName: $this->data['sur_name'] ?? '',
            dateOfBirth: $this->data['date_birth'] ?? '',
            phoneNumber: $this->data['phone_number'] ?? '',
            address: $this->data['address'] ?? '',
            city: $this->data['city'] ?? '',
            region: $this->data['region'] ?? '',
            postCode: $this->data['post_code'] ?? '',
            email: $this->data['email'] ?? '',
            countryCode: $this->data['country_code'] ?? '',
            binNumber: $this->data['bin_number'] ?? '',
            renewalPaymentFlag: $this->data['renewal_payment_flag'] ?? 1
        );
    }

    /**
     * Создает билдер для украинского резидента
     */
    public static function forUkraineResident(): self
    {
        return (new self())->withAddress('', '', 'UA', '');
    }

    /**
     * Создает билдер для US резидента
     */
    public static function forUSResident(): self
    {
        return (new self())->withAddress('', '', 'US', '');
    }

    /**
     * Создает билдер с тестовыми данными
     */
    public static function withTestData(): self
    {
        return (new self())
            ->withPersonalInfo('John', 'Doe', '01.01.1990', 'Test')
            ->withContactInfo('john.doe@example.com', '+380990000000')
            ->withAddress('Test Address 123', 'Kyiv', 'UA', '01001')
            ->withBinNumber('1234567890123456');
    }
}
