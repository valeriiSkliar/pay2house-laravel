<?php

namespace espolin\Pay2House\DTO;

abstract class BaseResponse
{
    /**
     * Создает объект из массива данных API ответа
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Конвертирует объект в массив
     */
    abstract public function toArray(): array;

    /**
     * Проверяет успешность операции
     */
    public function isSuccessful(): bool
    {
        return isset($this->status) && $this->status === 'success';
    }

    /**
     * Получает статус операции
     */
    public function getStatus(): ?string
    {
        return $this->status ?? null;
    }

    /**
     * Получает код ответа
     */
    public function getCode(): ?string
    {
        return $this->code ?? null;
    }

    /**
     * Конвертирует snake_case в camelCase
     */
    protected static function snakeToCamel(string $input): string
    {
        return lcfirst(str_replace('_', '', ucwords($input, '_')));
    }

    /**
     * Конвертирует camelCase в snake_case
     */
    protected static function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }

    /**
     * Магический метод для получения свойств в JSON формате
     */
    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
