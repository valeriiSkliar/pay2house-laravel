<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | API ключ для аутентификации в Pay2.House API.
    | Получить можно в личном кабинете Pay2.House.
    |
    */
    'api_key' => env('PAY2HOUSE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Базовый URL для API Pay2.House.
    | По умолчанию используется продакшн URL.
    |
    */
    'base_url' => env('PAY2HOUSE_BASE_URL', 'https://pay2.house/api'),

    /*
    |--------------------------------------------------------------------------
    | Default Merchant ID
    |--------------------------------------------------------------------------
    |
    | ID мерчанта по умолчанию для создания платежей.
    | Можно переопределить в каждом конкретном запросе.
    |
    */
    'default_merchant_id' => env('PAY2HOUSE_MERCHANT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | Валюта по умолчанию для операций.
    | Поддерживаемые валюты: USD, EUR, USDT
    |
    */
    'default_currency' => env('PAY2HOUSE_DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    |
    | Настройки таймаута для HTTP запросов к API.
    |
    */
    'timeout' => [
        'connect' => env('PAY2HOUSE_CONNECT_TIMEOUT', 10),
        'request' => env('PAY2HOUSE_REQUEST_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Настройки для обработки webhook уведомлений от Pay2.House.
    |
    */
    'webhook' => [
        'enabled' => env('PAY2HOUSE_WEBHOOK_ENABLED', false),
        'url' => env('PAY2HOUSE_WEBHOOK_URL', '/webhooks/pay2house'),
        'secret' => env('PAY2HOUSE_WEBHOOK_SECRET'),
        'verify_signature' => env('PAY2HOUSE_WEBHOOK_VERIFY_SIGNATURE', true),
        'log_requests' => env('PAY2HOUSE_WEBHOOK_LOG_REQUESTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payment Settings
    |--------------------------------------------------------------------------
    |
    | Настройки по умолчанию для создания платежей.
    |
    */
    'payment_defaults' => [
        'deadline_seconds' => env('PAY2HOUSE_DEFAULT_DEADLINE', 600), // 10 минут
        'payment_method' => env('PAY2HOUSE_DEFAULT_PAYMENT_METHOD', 'ALL'),
        'return_url' => env('PAY2HOUSE_DEFAULT_RETURN_URL'),
        'cancel_url' => env('PAY2HOUSE_DEFAULT_CANCEL_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Настройки логирования API запросов.
    |
    */
    'logging' => [
        'enabled' => env('PAY2HOUSE_LOGGING_ENABLED', false),
        'channel' => env('PAY2HOUSE_LOG_CHANNEL', 'default'),
        'level' => env('PAY2HOUSE_LOG_LEVEL', 'info'),
        'log_requests' => env('PAY2HOUSE_LOG_REQUESTS', true),
        'log_responses' => env('PAY2HOUSE_LOG_RESPONSES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Дополнительные правила валидации для входящих данных.
    |
    */
    'validation' => [
        'strict_mode' => env('PAY2HOUSE_STRICT_VALIDATION', false),
        'allowed_currencies' => ['USD', 'EUR', 'USDT'],
        'min_amount' => env('PAY2HOUSE_MIN_AMOUNT', 0.01),
        'max_amount' => env('PAY2HOUSE_MAX_AMOUNT', 1000000),
    ],
];
