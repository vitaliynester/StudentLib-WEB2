<?php


namespace App\Validator;

class PosterValidator extends BaseValidator
{
    /**
     * Проверить форму поста на ошибки
     * @param array $data (массив с входными данными для проверки)
     * @return bool (результат валидации)
     */
    public static function validate(array $data): bool
    {
        self::$errors = [];
        // Проверяем наличие всех обязательных полей
        self::checkContainsFields($data);
        // Если какое-либо поле отсутствует, то возвращаем ошибки и выходим
        if (count(self::$errors) != 0) {
            return false;
        }
        // Проверяем название поста на корректность
        self::validateTitle($data['title']);
        // Проверяем описание поста на корректность
        self::validateDescription($data['description']);
        // Если присутствуют ошибки, то возвращаем неудачу (FALSE)
        if (count(self::$errors) != 0) {
            return false;
        }
        // Если ошибок нет, то успех (TRUE)
        return true;
    }

    /**
     * Проверить переданный массив данных на наличие всех полей для сущности поста
     * @param array $data (массив с проверяемыми полями сущности поста)
     */
    protected static function checkContainsFields(array $data): void
    {
        if (!isset($data['title'])) {
            self::$errors['title'] = "Данное поле обязательно!";
        }
        if (!isset($data['description'])) {
            self::$errors['description'] = "Данное поле обязательно!";
        }
    }

    /**
     * Проверить название поста на корректность
     * @param string $title (название поста)
     */
    private static function validateTitle(string $title)
    {
        if (empty($title)) {
            self::$errors['title'] = "Укажите название поста";
            return;
        }
        if (strlen($title) >= 255) {
            self::$errors['title'] = "Максимальный размер названия 255 символов!";
        }
    }

    /**
     * Проверить описание поста на корректность
     * @param string $description (описание поста)
     */
    private static function validateDescription(string $description)
    {
        if (empty($description)) {
            self::$errors['description'] = "Укажите описание поста";
            return;
        }
        if (strlen($description) >= 5000) {
            self::$errors['description'] = "Максимальный размер описания 5000 символов!";
        }
    }

    /**
     * Получить массив полученных ошибок
     * @return array (массив ошибок)
     */
    public static function getErrors(): array
    {
        return self::$errors;
    }
}
