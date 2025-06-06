# Pay2House Laravel Package

Пакет для интеграции с API Pay2.House в Laravel приложениях.

## Установка

1. Установите пакет через Composer:

```bash
composer require your-vendor/pay2house-laravel
```

2. Опубликуйте конфигурационный файл:

```bash
php artisan vendor:publish --tag=pay2house-config
```

3. Настройте переменные окружения в `.env`:

```env
PAY2HOUSE_API_KEY=your-api-key
PAY2HOUSE_MERCHANT_ID=your-merchant-id
PAY2HOUSE_DEFAULT_CURRENCY=USD
PAY2HOUSE_WEBHOOK_ENABLED=true
PAY2HOUSE_WEBHOOK_SECRET=your-webhook-secret
```

## Быстрый старт

### Использование через фасад

```php
use YourVendor\Pay2House\Facades\Pay2House;

// Создание платежа
$payment = Pay2House::payments()->createSimplePayment(
    externalNumber: 'ORDER-123',
    amount: 100.00,
    currencyCode: 'USD',
    merchantId: config('pay2house.default_merchant_id'),
    description: 'Оплата заказа #123',
    returnUrl: 'https://example.com/success',
    cancelUrl: 'https://example.com/cancel'
);

// Перенаправление на оплату
return redirect($payment->getPaymentUrl());
```

### Использование через dependency injection

```php
use YourVendor\Pay2House\Services\PaymentService;

class PaymentController extends Controller
{
    public function createPayment(PaymentService $paymentService)
    {
        $payment = $paymentService->createSimplePayment(
            externalNumber: 'ORDER-123',
            amount: 100.00,
            currencyCode: 'USD',
            merchantId: config('pay2house.default_merchant_id'),
            description: 'Оплата заказа #123',
            returnUrl: route('payment.success'),
            cancelUrl: route('payment.cancel')
        );

        return response()->json([
            'payment_url' => $payment->getPaymentUrl(),
            'invoice_number' => $payment->getInvoiceNumber()
        ]);
    }
}
```

## Основные сервисы

### PaymentService - Работа с платежами

```php
use YourVendor\Pay2House\DTO\Payment\CreatePaymentRequest;

// Создание платежа с полными параметрами
$request = new CreatePaymentRequest(
    externalNumber: 'ORDER-123',
    amount: 100.00,
    currencyCode: 'USD',
    merchantId: 'your-merchant-id',
    description: 'Описание платежа',
    deadlineSeconds: 600,
    returnUrl: 'https://example.com/success',
    cancelUrl: 'https://example.com/cancel',
    handlingFee: 5.00,
    payerEmail: 'user@example.com',
    paymentMethod: 'ALL'
);

$payment = Pay2House::payments()->createPayment($request);

// Получение информации о платеже
$details = Pay2House::payments()->getPaymentDetails(
    new PaymentDetailsRequest('merchant-id', 'invoice-number')
);

// Проверка статуса платежа
$isPaid = Pay2House::payments()->isPaymentPaid('merchant-id', 'invoice-number');
```

### WalletService - Работа с кошельками

```php
// Получение списка кошельков
$wallets = Pay2House::wallets()->getActiveWallets();

// Создание нового кошелька
$wallet = Pay2House::wallets()->createWallet('My Wallet', 'USD');

// Получение информации о кошельке
$details = Pay2House::wallets()->getWalletDetails('P2U123456789');

// Получение баланса
$balance = Pay2House::wallets()->getWalletBalance('P2U123456789');

// Получение выписки (CSV)
$statementUrl = Pay2House::wallets()->getWalletStatement('P2U123456789');
```

### TransferService - Работа с переводами

```php
use YourVendor\Pay2House\DTO\Transfer\CreateTransferRequest;
use YourVendor\Pay2House\DTO\Transfer\TransferHistoryRequest;

// Простое создание перевода
$transfer = Pay2House::transfers()->createSimpleTransfer(
    senderAccount: 'P2U123456789',
    recipientAccount: 'P2U987654321',
    amount: 50.00,
    comment: 'Перевод средств'
);

// Создание перевода с полной валидацией
$request = new CreateTransferRequest(
    senderAccount: 'P2U123456789',
    recipientAccount: 'P2U987654321',
    amount: 50.00,
    comment: 'Детальный перевод'
);

if ($request->isValid()) {
    $transfer = Pay2House::transfers()->createTransfer($request);
    echo "Номер транзакции: " . $transfer->getTransactionNumber();
}

// Получение информации о переводе
$details = Pay2House::transfers()->getTransferByNumber('TN123456789');
echo "Статус: " . $details->getStatusInRussian();
echo "Сумма с комиссией: " . $details->getTotalAmount();

// Проверка статуса перевода
$isConfirmed = Pay2House::transfers()->isTransferConfirmed('TN123456789');

// Получение истории с фильтрацией
$historyRequest = TransferHistoryRequest::forLastMonth()
    ->onlyConfirmed()
    ->onlyOutgoing();

$history = Pay2House::transfers()->getTransferHistory($historyRequest);

// Статистика по истории
$stats = $history->getStatistics();
echo "Всего переводов: " . $stats['total_count'];
echo "Общая сумма: " . $stats['total_amount'];

// Быстрые методы
$recentTransfers = Pay2House::transfers()->getRecentTransfers(5);
$outgoingTransfers = Pay2House::transfers()->getOutgoingTransfers();
$incomingTransfers = Pay2House::transfers()->getIncomingTransfers();

// Получение переводов за период
$transfers = Pay2House::transfers()->getTransfersByDateRange(
    '01.12.2024',
    '31.12.2024'
);

// Группировка по датам
$groupedByDate = $transfers->groupByDate();

// Валидация без создания
$errors = Pay2House::transfers()->validateTransfer(
    'P2U123456789',
    'P2U987654321',
    100.00
);

if (empty($errors)) {
    echo "Перевод валиден";
}
```

### CardService - Работа с виртуальными картами

```php
use YourVendor\Pay2House\DTO\Card\IssueCardRequest;
use YourVendor\Pay2House\DTO\Card\CardBuilder;
use YourVendor\Pay2House\DTO\Card\GetCardsRequest;

// Получение списка карт
$activeCards = Pay2House::cards()->getActiveCards();
$blockedCards = Pay2House::cards()->getBlockedCards();

// Выпуск карты через билдер (рекомендуемый способ)
$cardRequest = CardBuilder::forUkraineResident()
    ->fromAccount('P2U123456789')
    ->withPersonalInfo('John', 'Doe', '01.01.1990', 'Smith')
    ->withContactInfo('john@example.com', '+380990000000')
    ->withAddress('Kyiv, Ukraine, Street 1', 'Kyiv', 'UA', '01001')
    ->withBinNumber('1234567890123456')
    ->withAutoRenewal(true)
    ->build();

if ($cardRequest->isValid()) {
    $card = Pay2House::cards()->issueCard($cardRequest);
    echo "ID карты: " . $card->getCardId();
    echo "Номер карты: " . $card->getMaskedCardNumber();
}

// Быстрый выпуск карты
$card = Pay2House::cards()->issueSimpleCard(
    accountNumber: 'P2U123456789',
    firstName: 'John',
    lastName: 'Doe',
    email: 'john@example.com',
    phone: '+380990000000',
    dateOfBirth: '01.01.1990',
    address: 'Kyiv, Ukraine',
    city: 'Kyiv',
    countryCode: 'UA',
    postCode: '01001',
    binNumber: '1234567890123456'
);

// Получение детальной информации о карте
$details = Pay2House::cards()->getCardById('VC123456789');

echo "Статус: " . $details->getStatusInRussian();
echo "Баланс: " . $details->balance . " " . $details->currencySymbol;
echo "Держатель: " . $details->getFullName();

// Проверка возможностей карты
if ($details->isActive() && $details->canTopUp(100)) {
    echo "Можно пополнить на 100";
}

if ($details->canRefund(50)) {
    echo "Можно вернуть 50";
}

// Пополнение карты
$topUp = Pay2House::cards()->topUpCardSimple('VC123456789', 'P2U123456789', 100.50);
echo "Номер транзакции: " . $topUp->getTransactionNumber();

// Возврат средств с карты
$refund = Pay2House::cards()->refundCardSimple('VC123456789', 50.00);

// История операций по карте
$history = Pay2House::cards()->getCardHistory(
    new CardHistoryRequest('VC123456789', perPage: 25, page: 1)
);

$stats = $history->getStatistics();
echo "Всего операций: " . $stats['total_count'];
echo "Общая сумма: " . $stats['total_amount'];

// Группировка операций по датам
$groupedByDate = $history->groupByDate();

// Управление картой
$balance = Pay2House::cards()->getRefreshedCardBalance('VC123456789');
$isActive = Pay2House::cards()->isCardActive('VC123456789');

// Блокировка и закрытие
Pay2House::cards()->blockCardById('VC123456789');
Pay2House::cards()->closeCardById('VC123456789'); // только при нулевом балансе

// Коды подтверждения для операций
$codes = Pay2House::cards()->getConfirmationCodes(
    new ConfirmationCodesRequest('VC123456789')
);

// Статистика по всем картам
$stats = Pay2House::cards()->getCardsStatistics();
/*
[
    'total_active' => 5,
    'total_blocked' => 1,
    'total_pending' => 0,
    'total_cards' => 6
]
*/

// Проверка лимитов и комиссий
$details = Pay2House::cards()->getCardById('VC123456789');
$topUpFee = $details->getTopUpFee(100); // Комиссия за пополнение на 100
$refundFee = $details->getRefundFee(50); // Комиссия за возврат 50

// Удобные проверки
$canTopUp = Pay2House::cards()->canTopUpCard('VC123456789', 100);
$canRefund = Pay2House::cards()->canRefundCard('VC123456789', 50);
```

### Продвинутая работа с картами

```php
// Создание карты с тестовыми данными
$testCard = CardBuilder::withTestData()
    ->fromAccount('P2U123456789')
    ->withBinNumber('1111222233334444')
    ->build();

// Получение карт с фильтрацией
$request = GetCardsRequest::forActiveCards(50, 1);
$cards = Pay2House::cards()->getCards($request);

// Статистика по картам
$cardsStats = $cards->getStatistics();
/*
[
    'total_count' => 25,
    'active_count' => 20,
    'blocked_count' => 3,
    'total_balance' => 15000.50,
    'currencies' => ['USD', 'EUR']
]
*/

// Группировка карт по валютам
$groupedByCurrency = $cards->groupByCurrency();
/*
[
    'USD' => [...],
    'EUR' => [...]
]
*/

// Работа с историей операций
$history = Pay2House::cards()->getCardHistory(
    new CardHistoryRequest('VC123456789')
);

$successfulTransactions = $history->getSuccessfulTransactions();
$totalAmount = $history->getTotalAmount();
$groupedByDate = $history->groupByDate();

// Валидация данных карты
$cardRequest = new IssueCardRequest(
    accountNumber: 'invalid', // Неверный формат
    firstName: 'A', // Слишком короткое имя
    // ... другие параметры
);

$errors = $cardRequest->validate();
/*
[
    'Invalid account number format (should start with P2U)',
    'First name must be at least 2 characters long',
    'Card holder must be at least 18 years old'
]
*/

// Проверка возраста держателя карты
$age = $cardRequest->getAge(); // null если дата некорректна
if ($age && $age >= 18) {
    echo "Возраст подходит: {$age} лет";
}

// Получение полного имени
$fullName = $cardRequest->getFullName(); // "John Smith Doe"

// Детальная информация о блокировке
$details = Pay2House::cards()->getCardById('VC123456789');
if ($details->isBlocked()) {
    echo "Причина блокировки: " . $details->getBlockStatusInRussian();
}

// Проверка срока действия карты
if ($details->isExpiringSoon(30)) { // Истекает в течение 30 дней
    echo "Карта скоро истечет: " . $details->getRenewalAt()->diffForHumans();
}
```

## Обработка ошибок

```php
use YourVendor\Pay2House\Exceptions\Pay2HouseException;

try {
    $payment = Pay2House::payments()->createSimplePayment(...);
} catch (Pay2HouseException $e) {
    if ($e->isAuthenticationError()) {
        // Ошибка аутентификации
        Log::error('Pay2House auth error: ' . $e->getMessage());
    } elseif ($e->isInsufficientFundsError()) {
        // Недостаточно средств
        return response()->json(['error' => 'Недостаточно средств'], 400);
    } elseif ($e->isValidationError()) {
        // Ошибка валидации
        return response()->json(['error' => $e->getMessage()], 422);
    } else {
        // Другие ошибки
        Log::error('Pay2House error: ' . $e->getMessage());
    }
}
```

## Webhook обработка

```php
// В контроллере для обработки webhook'ов
use YourVendor\Pay2House\Support\SignatureGenerator;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('Pay2-House-Signature');
        $payload = $request->getContent();

        $generator = new SignatureGenerator();
        $decryptedData = $generator->decryptWebhook($signature, config('pay2house.api_key'));

        if ($decryptedData) {
            $webhookData = json_decode($decryptedData, true);

            // Обработка уведомления
            if ($webhookData['status'] === 'paid') {
                // Платеж оплачен
                $this->handlePaidPayment($webhookData);
            }
        }

        return response('OK', 200);
    }
}
```

## Валидация данных

Все DTO классы включают встроенную валидацию:

```php
$request = new CreatePaymentRequest(...);

if (!$request->isValid()) {
    $errors = $request->validate();
    return response()->json(['errors' => $errors], 422);
}
```

## Логирование

Включите логирование в конфигурации:

```env
PAY2HOUSE_LOGGING_ENABLED=true
PAY2HOUSE_LOG_CHANNEL=pay2house
PAY2HOUSE_LOG_REQUESTS=true
PAY2HOUSE_LOG_RESPONSES=true
```

## Расширение функциональности

Библиотека построена с учетом возможности расширения. Вы можете:

1. Создавать собственные сервисы, наследуя `BaseApiClient`
2. Добавлять новые DTO классы
3. Переопределять методы существующих сервисов

Пример создания собственного сервиса:

```php
use YourVendor\Pay2House\Client\BaseApiClient;

class CustomService extends BaseApiClient
{
    public function customMethod(array $data): array
    {
        return $this->post('custom/endpoint', $data);
    }
}
```

## Полный пример использования всех сервисов

````php
use YourVendor\Pay2House\Facades\Pay2House;

// 1. Создание кошелька
$wallet = Pay2House::wallets()->createWallet('My Business Wallet', 'USD');
$accountNumber = $wallet['account_number'];

// 2. Создание платежа для пополнения кошелька
$payment = Pay2House::payments()->createSimplePayment(
    externalNumber: 'DEPOSIT-001',
    amount: 1000.00,
    currencyCode: 'USD',
    merchantId: config('pay2house.default_merchant_id'),
    description: 'Пополнение бизнес кошелька',
    returnUrl: route('payment.success'),
    cancelUrl: route('payment.cancel')
);

// 3. После поступления средств - выпуск виртуальной карты
$card = CardBuilder::forUkraineResident()
    ->fromAccount($accountNumber)
    ->withPersonalInfo('John', 'Doe', '01.01.1990')
    ->withContactInfo('john@example.com', '+380990000000')
    ->withAddress('Kyiv, Ukraine', 'Kyiv', 'UA', '01001')
    ->withBinNumber('1234567890123456')
    ->build();

$issuedCard = Pay2House::cards()->issueCard($card);

// 4. Пополнение карты
$topUp = Pay2House::cards()->topUpCardSimple(
    $issuedCard->getCardId(),
    $accountNumber,
    500.00
);

// 5. Внутренний перевод между кошельками
$transfer = Pay2House::transfers()->createSimpleTransfer(
    senderAccount: $accountNumber,
    recipientAccount: 'P2U987654321',
    amount: 200.00,
    comment: 'Оплата за услуги'
);

// 6. Проверка статусов всех операций
$paymentStatus = Pay2House::payments()->isPaymentPaid(
    config('pay2house.default_merchant_id'),
    $payment->getInvoiceNumber()
);

$transferStatus = Pay2House::transfers()->isTransferConfirmed(
    $transfer->getTransactionNumber()
);

$cardBalance = Pay2House::cards()->getCardBalance(
    $issuedCard->getCardId()
);

## Тестирование

Пакет включает полный набор тестов:

```php
// Feature тесты сервисов
php artisan test tests/Feature/PaymentServiceTest.php
php artisan test tests/Feature/TransferServiceTest.php
php artisan test tests/Feature/CardServiceTest.php

// Unit тесты компонентов
php artisan test tests/Unit/CardBuilderTest.php
php artisan test tests/Unit/SignatureGeneratorTest.php

// Пример мокирования для тестов
use YourVendor\Pay2House\Services\CardService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

$mock = new MockHandler([
    new Response(200, [], json_encode([
        'status' => 'success',
        'card_id' => 'VC123456789'
    ]))
]);

$cardService = new CardService('test-key');
// Замена HTTP клиента через рефлексию для тестов
````

## Архитектура и расширение

Библиотека построена по принципам SOLID и позволяет легко расширять функциональность:

### Добавление нового API метода

1. Создайте DTO классы для запроса и ответа
2. Добавьте метод в соответствующий сервис
3. Добавьте валидацию в DTO классы
4. Создайте тесты для нового функционала

### Создание собственного сервиса

```php
use YourVendor\Pay2House\Client\BaseApiClient;

class CustomService extends BaseApiClient
{
    public function customMethod(array $data): array
    {
        return $this->post('custom/endpoint', $data);
    }
}
```

## Производительность и кэширование

```php
// Кэширование результатов для часто используемых данных
$balance = Cache::remember("wallet.{$accountNumber}.balance", 300, function() use ($accountNumber) {
    return Pay2House::wallets()->getWalletBalance($accountNumber);
});

// Асинхронная обработка больших операций
dispatch(new ProcessCardIssueJob($cardRequest));
```

## Поддержка

Если у вас есть вопросы или предложения, создайте issue в репозитории проекта.

## Лицензия

MIT License
