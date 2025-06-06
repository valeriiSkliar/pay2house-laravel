<?php

namespace espolin\Pay2House\DTO;

abstract class BaseRequest
{
    /**
     * Конвертирует объект в массив для API запроса
     */
    abstract public function toArray(): array;

    /**
     * Валидирует данные запроса
     * Возвращает массив ошибок валидации
     */
    public function validate(): array
    {
        return [];
    }

    /**
     * Проверяет валидность запроса
     */
    public function isValid(): bool
    {
        return empty($this->validate());
    }

    /**
     * Получает первую ошибку валидации
     */
    public function getFirstError(): ?string
    {
        $errors = $this->validate();
        return $errors[0] ?? null;
    }

    /**
     * Создает экземпляр из массива данных
     */
    public static function fromArray(array $data): static
    {
        $reflection = new \ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new static();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $paramName = $param->getName();
            $snakeCaseName = self::camelToSnake($paramName);

            if (array_key_exists($paramName, $data)) {
                $args[] = $data[$paramName];
            } elseif (array_key_exists($snakeCaseName, $data)) {
                $args[] = $data[$snakeCaseName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }

        return new static(...$args);
    }

    /**
     * Конвертирует camelCase в snake_case
     */
    protected static function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
}
