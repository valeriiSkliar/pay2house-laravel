<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;

class IssueCardRequest extends BaseRequest
{
    public function __construct(
        public string $accountNumber,
        public string $firstName,
        public string $lastName,
        public string $surName,
        public string $dateOfBirth,
        public string $phoneNumber,
        public string $address,
        public string $city,
        public string $region,
        public string $postCode,
        public string $email,
        public string $countryCode,
        public string $binNumber,
        public int $renewalPaymentFlag = 1
    ) {}

    public function toArray(): array
    {
        return [
            'account_number' => $this->accountNumber,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sur_name' => $this->surName,
            'date_birth' => $this->dateOfBirth,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'city' => $this->city,
            'region' => $this->region,
            'post_code' => $this->postCode,
            'email' => $this->email,
            'country_code' => $this->countryCode,
            'bin_number' => $this->binNumber,
            'renewal_payment_flag' => $this->renewalPaymentFlag,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        // Проверка номера счета
        if (empty($this->accountNumber)) {
            $errors[] = 'Account number is required';
        } elseif (!preg_match('/^P2U\d+$/', $this->accountNumber)) {
            $errors[] = 'Invalid account number format (should start with P2U)';
        }

        // Проверка имени
        if (empty($this->firstName) || strlen($this->firstName) < 2) {
            $errors[] = 'First name must be at least 2 characters long';
        }

        // Проверка фамилии
        if (empty($this->lastName) || strlen($this->lastName) < 2) {
            $errors[] = 'Last name must be at least 2 characters long';
        }

        // Проверка даты рождения
        if (empty($this->dateOfBirth)) {
            $errors[] = 'Date of birth is required';
        } elseif (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $this->dateOfBirth)) {
            $errors[] = 'Date of birth must be in DD.MM.YYYY format';
        } else {
            // Проверяем что дата валидна и возраст больше 18 лет
            try {
                $birthDate = \DateTime::createFromFormat('d.m.Y', $this->dateOfBirth);
                if (!$birthDate) {
                    $errors[] = 'Invalid date of birth';
                } else {
                    $age = $birthDate->diff(new \DateTime())->y;
                    if ($age < 18) {
                        $errors[] = 'Card holder must be at least 18 years old';
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid date of birth format';
            }
        }

        // Проверка телефона
        if (empty($this->phoneNumber)) {
            $errors[] = 'Phone number is required';
        } elseif (!preg_match('/^\+\d{10,15}$/', $this->phoneNumber)) {
            $errors[] = 'Phone number must include country code and be 10-15 digits';
        }

        // Проверка email
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email address is required';
        }

        // Проверка адреса
        if (empty($this->address) || strlen($this->address) < 5) {
            $errors[] = 'Address must be at least 5 characters long';
        }

        // Проверка города
        if (empty($this->city) || strlen($this->city) < 2) {
            $errors[] = 'City must be at least 2 characters long';
        }

        // Проверка почтового кода
        if (empty($this->postCode)) {
            $errors[] = 'Post code is required';
        }

        // Проверка кода страны
        if (empty($this->countryCode) || strlen($this->countryCode) !== 2) {
            $errors[] = 'Country code must be 2 characters (ISO 3166-1 alpha-2)';
        }

        // Проверка BIN номера
        if (empty($this->binNumber)) {
            $errors[] = 'BIN number is required';
        } elseif (!preg_match('/^\d{16}$/', $this->binNumber)) {
            $errors[] = 'BIN number must be 16 digits';
        }

        // Проверка флага renewal
        if (!in_array($this->renewalPaymentFlag, [0, 1])) {
            $errors[] = 'Renewal payment flag must be 0 or 1';
        }

        return $errors;
    }

    /**
     * Устанавливает автоматическое продление
     */
    public function withAutoRenewal(bool $enabled = true): self
    {
        $this->renewalPaymentFlag = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Получает полное имя держателя карты
     */
    public function getFullName(): string
    {
        $parts = array_filter([$this->firstName, $this->surName, $this->lastName]);
        return implode(' ', $parts);
    }

    /**
     * Проверяет корректность возраста
     */
    public function getAge(): ?int
    {
        try {
            $birthDate = \DateTime::createFromFormat('d.m.Y', $this->dateOfBirth);
            if ($birthDate) {
                return $birthDate->diff(new \DateTime())->y;
            }
        } catch (\Exception $e) {
            // Игнорируем ошибку
        }

        return null;
    }
}
