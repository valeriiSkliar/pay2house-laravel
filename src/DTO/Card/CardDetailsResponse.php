<?php

namespace espolin\Pay2House\DTO\Card;

use espolin\Pay2House\DTO\BaseRequest;
use espolin\Pay2House\DTO\BaseResponse;

class CardDetailsResponse extends BaseResponse
{
    public function __construct(
        public string $status,
        public string $code,
        public string $cardName,
        public string $cardNumber,
        public string $cardId,
        public string $cvv,
        public string $expirationDate,
        public string $password,
        public string $currencyCode,
        public string $currencySymbol,
        public int $timeCreated,
        public string $dateCreated,
        public int $timeRenewal,
        public string $dateRenewal,
        public string $email,
        public string $phoneNumber,
        public string $addressLine1,
        public string $city,
        public string $countryCode,
        public string $postCode,
        public string $firstName,
        public string $lastName,
        public string $surName,
        public string $dateBirth,
        public string $cardStatus,
        public float $balance,
        public float $issuanceCost,
        public float $renewalCost,
        public float $rechargeCost,
        public string $rechargeType,
        public float $accountRefundCost,
        public string $accountRefundType,
        public float $refundMinAmount,
        public float $minRechargeAmount,
        public float $maxRechargeAmount,
        public string $blockStatus,
        public ?int $timeBlocked = null,
        public ?string $dateBlocked = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            code: $data['code'],
            cardName: $data['card_name'] ?? '',
            cardNumber: $data['card_number'],
            cardId: $data['card_id'],
            cvv: $data['cvv'],
            expirationDate: $data['expiration_date'],
            password: $data['password'] ?? '',
            currencyCode: $data['currency_code'],
            currencySymbol: $data['currency_symbol'],
            timeCreated: (int) $data['time_created'],
            dateCreated: $data['date_created'],
            timeRenewal: (int) $data['time_renewal'],
            dateRenewal: $data['date_renewal'],
            email: $data['email'],
            phoneNumber: $data['phone_number'],
            addressLine1: $data['address_line_1'],
            city: $data['city'],
            countryCode: $data['country_code'],
            postCode: $data['post_code'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            surName: $data['sur_name'] ?? '',
            dateBirth: $data['date_birth'],
            cardStatus: $data['card_status'],
            balance: (float) $data['balance'],
            issuanceCost: (float) $data['issuance_cost'],
            renewalCost: (float) $data['renewal_cost'],
            rechargeCost: (float) $data['recharge_cost'],
            rechargeType: $data['recharge_type'],
            accountRefundCost: (float) $data['account_refund_cost'],
            accountRefundType: $data['account_refund_type'],
            refundMinAmount: (float) $data['refund_min_amount'],
            minRechargeAmount: (float) $data['min_recharge_amount'],
            maxRechargeAmount: (float) $data['max_recharge_amount'],
            blockStatus: $data['block_status'],
            timeBlocked: isset($data['time_blocked']) ? (int) $data['time_blocked'] : null,
            dateBlocked: $data['date_blocked'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'card_name' => $this->cardName,
            'card_number' => $this->cardNumber,
            'card_id' => $this->cardId,
            'cvv' => $this->cvv,
            'expiration_date' => $this->expirationDate,
            'password' => $this->password,
            'currency_code' => $this->currencyCode,
            'currency_symbol' => $this->currencySymbol,
            'time_created' => $this->timeCreated,
            'date_created' => $this->dateCreated,
            'time_renewal' => $this->timeRenewal,
            'date_renewal' => $this->dateRenewal,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'address_line_1' => $this->addressLine1,
            'city' => $this->city,
            'country_code' => $this->countryCode,
            'post_code' => $this->postCode,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sur_name' => $this->surName,
            'date_birth' => $this->dateBirth,
            'card_status' => $this->cardStatus,
            'balance' => $this->balance,
            'issuance_cost' => $this->issuanceCost,
            'renewal_cost' => $this->renewalCost,
            'recharge_cost' => $this->rechargeCost,
            'recharge_type' => $this->rechargeType,
            'account_refund_cost' => $this->accountRefundCost,
            'account_refund_type' => $this->accountRefundType,
            'refund_min_amount' => $this->refundMinAmount,
            'min_recharge_amount' => $this->minRechargeAmount,
            'max_recharge_amount' => $this->maxRechargeAmount,
            'block_status' => $this->blockStatus,
            'time_blocked' => $this->timeBlocked,
            'date_blocked' => $this->dateBlocked,
        ];
    }

    /**
     * Проверяет активна ли карта
     */
    public function isActive(): bool
    {
        return $this->cardStatus === 'active';
    }

    /**
     * Проверяет заблокирована ли карта
     */
    public function isBlocked(): bool
    {
        return $this->cardStatus === 'blocked';
    }

    /**
     * Проверяет закрыта ли карта
     */
    public function isClosed(): bool
    {
        return $this->cardStatus === 'closed';
    }

    /**
     * Проверяет находится ли карта в ожидании выпуска
     */
    public function isAwaiting(): bool
    {
        return $this->cardStatus === 'awaiting';
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

    /**
     * Получает полное имя держателя карты
     */
    public function getFullName(): string
    {
        $parts = array_filter([$this->firstName, $this->surName, $this->lastName]);
        return implode(' ', $parts);
    }

    /**
     * Получает дату создания как объект Carbon
     */
    public function getCreatedAt(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromTimestamp($this->timeCreated);
    }

    /**
     * Получает дату продления как объект Carbon
     */
    public function getRenewalAt(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromTimestamp($this->timeRenewal);
    }

    /**
     * Проверяет скоро ли истекает карта
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->getRenewalAt()->diffInDays(now()) <= $days;
    }

    /**
     * Получает комиссию за пополнение
     */
    public function getTopUpFee(float $amount): float
    {
        if ($this->rechargeType === 'percent') {
            return $amount * ($this->rechargeCost / 100);
        }

        return $this->rechargeCost; // fixed fee
    }

    /**
     * Получает комиссию за возврат
     */
    public function getRefundFee(float $amount): float
    {
        if ($this->accountRefundType === 'percent') {
            return $amount * ($this->accountRefundCost / 100);
        }

        return $this->accountRefundCost; // fixed fee
    }

    /**
     * Проверяет можно ли пополнить на указанную сумму
     */
    public function canTopUp(float $amount): bool
    {
        return $this->isActive()
            && $amount >= $this->minRechargeAmount
            && $amount <= $this->maxRechargeAmount;
    }

    /**
     * Проверяет можно ли сделать возврат указанной суммы
     */
    public function canRefund(float $amount): bool
    {
        return $this->isActive()
            && $amount >= $this->refundMinAmount
            && $amount <= $this->balance;
    }

    /**
     * Получает статус карты на русском языке
     */
    public function getStatusInRussian(): string
    {
        return match ($this->cardStatus) {
            'active' => 'Активна',
            'blocked' => 'Заблокирована',
            'closed' => 'Закрыта',
            'awaiting' => 'Ожидает выпуска',
            default => 'Неизвестный статус'
        };
    }

    /**
     * Получает статус блокировки на русском языке
     */
    public function getBlockStatusInRussian(): string
    {
        return match ($this->blockStatus) {
            'terms_violation' => 'Нарушение условий использования',
            'insufficient_funds' => 'Недостаточно средств',
            'unauthorized_topup' => 'Несанкционированные попытки пополнения',
            'high_refund_rate' => 'Высокий процент возвратов',
            'prebilling' => 'Первое списание',
            'subscription_delay' => 'Задержка платежа по подписке',
            'invalid_personal_data' => 'Некорректные персональные данные',
            'illegal_activity' => 'Попытки незаконных действий',
            'verification_issues' => 'Проблемы с верификацией',
            'high_risk' => 'Высокий уровень риска',
            'terms_change' => 'Изменение условий карты',
            'inactivity' => 'Неактивность',
            'internal_errors' => 'Внутренние ошибки',
            'regulatory_requirements' => 'Регуляторные требования',
            'user_blocked' => 'Заблокирована клиентом',
            'not_blocked' => 'Не заблокирована',
            default => 'Неизвестная причина'
        };
    }
}
