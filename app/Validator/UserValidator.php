<?php


namespace App\Validator;

use App\Repository\UserRepository;

class UserValidator extends BaseValidator
{
    /**
     * Проверить форму пользователя на ошибки
     * @param array $data (массив с входными данными для проверки)
     * @return bool (результат валидации)
     */
    public static function validate(array $data): bool
    {
        self::$errors = array();
        // Проверяем наличие всех обязательных полей
        self::checkContainsFields($data);
        // Если какое-либо поле отсутствует, то возвращаем ошибки и выходим
        if (count(self::$errors) != 0) {
            return false;
        }
        // Проверяем существование пользователя в БД
        self::checkExists($data['email']);
        if (count(self::$errors) != 0) {
            return false;
        }
        // Если все обязательные поля присутствуют, то проверяем их на корректность
        if (isset($data['patronymic'])) {
            self::validateUserName($data['first_name'], $data['last_name'], $data['patronymic']);
        } else {
            self::validateUserName($data['first_name'], $data['last_name']);
        }
        self::validateEmail($data['email']);
        self::validatePassword($data['password'], $data['password_confirm']);
        $validateResult = self::$errors;
        return count($validateResult) == 0;
    }

    /**
     * Проверить переданный массив данных на наличие всех полей для сущности пользователя
     * @param array $data (массив с проверяемыми полями сущности пользователя)
     */
    protected static function checkContainsFields(array $data): void
    {
        if (!isset($data['last_name'])) {
            self::$errors['last_name'] = "Данное поле обязательно!";
        }
        if (!isset($data['first_name'])) {
            self::$errors['first_name'] = "Данное поле обязательно!";
        }
        if (!isset($data['email'])) {
            self::$errors['email'] = "Данное поле обязательно!";
        }
        if (!isset($data['password'])) {
            self::$errors['password'] = "Данное поле обязательно!";
        }
        if (!isset($data['password_confirm'])) {
            self::$errors['password_confirm'] = "Данное поле обязательно!";
        }
    }

    /**
     * Проверка существования пользователя в БД по почте
     * @param string $email (почта пользователя)
     */
    private static function checkExists(string $email)
    {
        $repos = new UserRepository();
        $exist = $repos->checkUserByEmail($email);
        if ($exist) {
            self::$errors['email'] = "Данная почта уже используется!";
        }
    }

    /**
     * Проверить ФИО пользователя на корректность
     * @param string $firstName (имя пользователя)
     * @param string $lastName (фамилия пользователя)
     * @param string $patronymic (отчество пользователя, по умолчанию пусто)
     */
    private static function validateUserName(string $firstName, string $lastName, string $patronymic = "")
    {
        if (!preg_match('/^[а-яё -]+$/ui', $firstName) || empty($firstName)) {
            self::$errors['first_name'] = "Введите корректное имя";
        } elseif (!preg_match('/^[а-яё -]+$/ui', $lastName) || empty($lastName)) {
            self::$errors['last_name'] = "Введите корректную фамилию";
        } elseif (!empty($patronymic)) {
            if (!preg_match('/^[а-яё -]+$/ui', $patronymic)) {
                self::$errors['patronymic'] = "Введите корректное отчество";
            }
        }
    }

    /**
     * Проверить почту пользователя на корректность
     * @param string $email (почта пользователя)
     */
    private static function validateEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::$errors['email'] = "Введите корректную почту";
        }
    }

    /**
     * Проверить пароль на корректность
     * @param string $password (пароль пользователя)
     * @param string $repeatPassword (пароль подтверждения)
     */
    private static function validatePassword(string $password, string $repeatPassword)
    {
        if ($password != $repeatPassword) {
            self::$errors['password_confirm'] = "Пароли не совпадают!";
            return;
        }
        if (preg_match('/[0-9]+/', $password) && !preg_match('/[A-zА-я]+/', $password)) {
            self::$errors['password'] = "Пароль должен состоять не только из цифр!";
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
