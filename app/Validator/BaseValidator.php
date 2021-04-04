<?php

namespace App\Validator;

abstract class BaseValidator
{
    /**
     * @var array (массив с ошибками определенной формы)
     */
    protected static array $errors;

    /**
     * Проверить форму на ошибки
     * @param array $data (массив с входными данными для проверки)
     * @return bool (успешность валидации)
     */
    abstract public static function validate(array $data): bool;

    /**
     * Получить массив полученных ошибок
     * @return array (массив ошибок)
     */
    abstract public static function getErrors(): array;

    /**
     * Проверить переданный массив данных на наличие всех полей для сущности
     * @param array $data (массив с проверяемыми полями сущности)
     */
    abstract protected static function checkContainsFields(array $data): void;
}
