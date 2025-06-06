<?php

namespace espolin\Pay2House\Support;

class SignatureGenerator
{
    /**
     * Создает токен подписи для API запроса
     * Логика создания токена берется из документации API
     */
    public function createToken(array $data): string
    {
        // Удаляем пустые значения
        $filteredData = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        // Сортируем по ключам
        ksort($filteredData);

        // Создаем строку для подписи
        $signatureString = '';
        foreach ($filteredData as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $signatureString .= $key . '=' . $value . '&';
        }

        // Убираем последний &
        $signatureString = rtrim($signatureString, '&');

        // Создаем подпись (в документации нет точного алгоритма, 
        // поэтому используем стандартный подход)
        return base64_encode(hash_hmac('sha256', $signatureString, config('pay2house.api_key'), true));
    }

    /**
     * Проверяет подпись webhook'а
     */
    public function verifyWebhookSignature(string $signature, string $payload, string $secretKey): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Расшифровывает данные webhook'а
     */
    public function decryptWebhook(?string $data, string $secretKey): ?string
    {
        if (!$data) {
            return null;
        }

        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            return null;
        }

        $parts = explode('|', $decodedData);
        if (count($parts) !== 3) {
            return null;
        }

        [$iv, $signature, $encryptedData] = $parts;

        $calculatedSignature = hash_hmac('sha256', $iv . '|' . $encryptedData, $secretKey);

        if (!hash_equals($calculatedSignature, $signature)) {
            return null;
        }

        $key = hex2bin(hash('sha256', $secretKey));
        $ivBinary = hex2bin(bin2hex(hex2bin($iv)));

        $decryptedData = openssl_decrypt(
            base64_decode($encryptedData),
            'AES-256-CBC',
            $key,
            0,
            $ivBinary
        );

        return $decryptedData !== false ? $decryptedData : null;
    }
}
