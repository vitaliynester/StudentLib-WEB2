<?php


namespace App\Validator;

class FileValidator extends BaseValidator
{
    /**
     * Список допустимых расширений и форматов файла
     * @var array (массив расширений и форматов. Пример: [
     *      "doc" => "application/msword",
     *      "xls" => "application/vnd.ms-excel",
     *      "jpg" => "image/jpeg"
     * ])
     */
    private static array $types;

    /**
     * Проверить форму для файла на ошибки
     * @param array $data (массив с входными данными для проверки)
     * @return bool (результат валидации)
     */
    public static function validate(array $data): bool
    {
        self::$errors = [];
        // Получаем список всех допустимых расширений и их форматов
        self::$types = require_once dirname(__DIR__, 1) . "/Helper/files_extension_constants.php";
        // Проходимся по каждому расширению и проверяем на допустимость
        foreach ($data as $file) {
            self::validateType($file['tmp_name'], $file['extension']);
        }
        // Если отсутствуют ошибки, то возвращаем успех (TRUE)
        if (count(self::$errors) == 0) {
            return true;
        }
        // Если ошибки есть, то возвращаем неудачу (FALSE)
        return false;
    }

    /**
     * Проверить переданный файл на допустимый тип
     * @param string $path (путь до проверяемого файла)
     * @param string $extension (расширение передаваемого файла)
     */
    private static function validateType(string $path, string $extension)
    {
        // Получаем тип файла по его бинарному содержимому
        $fileType = mime_content_type($path);
        // Проверяем допустимость использования данного типа данных
        if (self::$types[$extension] != $fileType) {
            self::$errors['file'] = "Данный формат файла не поддерживается (" . $extension . ")";
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

    /**
     * Проверить переданный массив данных на наличие всех полей для сущности
     * @param array $data (массив с проверяемыми полями сущности)
     */
    protected static function checkContainsFields(array $data): void
    {
    }
}
