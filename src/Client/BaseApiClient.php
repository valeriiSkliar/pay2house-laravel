<?php

namespace espolin\Pay2House\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use espolin\Pay2House\Exceptions\Pay2HouseException;
use espolin\Pay2House\Support\SignatureGenerator;

abstract class BaseApiClient
{
    protected Client $httpClient;
    protected string $apiKey;
    protected string $baseUrl;
    protected SignatureGenerator $signatureGenerator;

    public function __construct(string $apiKey, string $baseUrl = 'https://pay2.house/api')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);
        $this->signatureGenerator = new SignatureGenerator();
    }

    /**
     * Выполняет POST запрос к API
     * 
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws Pay2HouseException
     */
    protected function post(string $endpoint, array $data = []): array
    {
        try {
            $signToken = $this->signatureGenerator->createToken($data);

            $requestData = [
                'sign_token' => $signToken,
                'api_key' => $this->apiKey,
            ];

            $response = $this->httpClient->post($this->baseUrl . '/' . ltrim($endpoint, '/'), [
                'form_params' => $requestData,
                'headers' => [
                    'User-Agent' => 'Pay2House-Laravel-Client/1.0',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Pay2HouseException('Invalid JSON response from API');
            }

            if (!isset($data['status'])) {
                throw new Pay2HouseException('Invalid API response format');
            }

            if ($data['status'] !== 'success') {
                $errorCode = $data['code'] ?? 'UNKNOWN_ERROR';
                $errorMessage = $data['msg'] ?? 'Unknown error occurred';
                throw new Pay2HouseException($errorMessage, $errorCode);
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new Pay2HouseException('HTTP request failed: ' . $e->getMessage());
        }
    }

    /**
     * Получает базовую конфигурацию для запросов
     */
    protected function getBaseConfig(): array
    {
        return [
            'api_key' => $this->apiKey,
            'base_url' => $this->baseUrl,
        ];
    }
}
