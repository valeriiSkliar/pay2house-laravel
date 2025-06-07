# Pay2House Laravel SDK

Laravel package for Pay2House payment gateway integration.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pay2house/laravel-sdk.svg?style=flat-square)](https://packagist.org/packages/pay2house/laravel-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/pay2house/laravel-sdk.svg?style=flat-square)](https://packagist.org/packages/pay2house/laravel-sdk)

## Installation

You can install the package via composer:

```bash
composer require espolin/pay2house-laravel-sdk
```

## Configuration

Publish and edit the configuration file:

```bash
php artisan vendor:publish --provider="espolin\Laravel\Pay2HouseServiceProvider"
```

Add your Pay2House credentials to your `.env` file:

```env
PAY2HOUSE_API_URL=https://api.pay2house.com
PAY2HOUSE_MERCHANT_ID=your_merchant_id
PAY2HOUSE_API_KEY=your_api_key
PAY2HOUSE_SECRET_KEY=your_secret_key
PAY2HOUSE_WEBHOOK_SECRET=your_webhook_secret
```

## Usage

### Payments

```php
use espolin\Laravel\Facades\Pay2House;

// Create a payment
$payment = Pay2House::payments()->create([
    'amount' => 100.00,
    'currency' => 'RUB',
    'description' => 'Payment description',
    'return_url' => 'https://your-site.com/success',
    'cancel_url' => 'https://your-site.com/cancel',
]);

// Get payment details
$details = Pay2House::payments()->getDetails($payment->id);
```

### Wallets

```php
// Get wallets
$wallets = Pay2House::wallets()->getWallets();

// Create wallet
$wallet = Pay2House::wallets()->create([
    'currency' => 'RUB',
    'name' => 'My Wallet'
]);

// Get wallet statement
$statement = Pay2House::wallets()->getStatement($wallet->id, [
    'from' => '2024-01-01',
    'to' => '2024-01-31'
]);
```

### Transfers

```php
// Create transfer
$transfer = Pay2House::transfers()->create([
    'from_wallet_id' => 'wallet_123',
    'to_wallet_id' => 'wallet_456',
    'amount' => 50.00,
    'currency' => 'RUB',
    'description' => 'Transfer description'
]);

// Get transfer details
$details = Pay2House::transfers()->getDetails($transfer->id);
```

### Cards

```php
// Issue a card
$card = Pay2House::cards()->issue([
    'wallet_id' => 'wallet_123',
    'cardholder_name' => 'John Doe',
    'delivery_address' => [
        'country' => 'RU',
        'city' => 'Moscow',
        'address' => 'Red Square 1'
    ]
]);

// Top up card
$topup = Pay2House::cards()->topUp($card->id, [
    'amount' => 100.00,
    'currency' => 'RUB'
]);
```

## Webhooks

The package automatically registers webhook routes. You can customize webhook handling by extending the `WebhookController`:

```php
php artisan vendor:publish --tag=pay2house-controllers
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Pay2House Team](https://github.com/espolindev/pay2house-laravel-sdk/graphs/contributors)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
