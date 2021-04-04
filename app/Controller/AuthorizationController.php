<?php


namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Services\Router;
use App\Validator\UserValidator;
use Exception;

class AuthorizationController
{
    /**
     * Обработчик POST запроса на регистрацию
     * @param array $data (поля необходимые для регистрации пользователя)
     */
    public function register(array $data)
    {
        // Валидируем поля переданные в POST запросе
        $valid = UserValidator::validate($data);
        // Если проверка полей прошла успешно, то
        if ($valid) {
            // Создаем экземпляр сущности пользователя
            $user = new UserEntity($data);
            // Пытаемся сохранить его в БД
            if (!$user->save()) {
                Router::errorPage(500);
            }
            // После успешной регистрации убираем ошибки и заполненные поля в форме
            unset($_SESSION['errors']);
            unset($_SESSION['user_data']);
            // Перенаправляем на страницу авторизации
            Router::redirect('/login');
        } else {
            // Если проверка полей при регистрации завершилась с ошибкой, то
            // Получаем список ошибок и сохраняем их вместе с заполненными данными
            $_SESSION['errors'] = UserValidator::getErrors();
            $_SESSION['user_data'] = $data;
            // Перенаправляем обратно на страницу регистрации
            Router::redirect('/register');
        }
    }

    /**
     * Обработчик POST запроса на авторизацию
     * @param array $data (поля необходимые для авторизации пользователя)
     */
    public function login(array $data)
    {
        // Переносим полученные данные в локальные переменные
        $email = $data['email'];
        $password = $data['password'];
        try {
            // Создаем новый экземпляр пользовательского репозитория
            $repos = new UserRepository();
            // Пытаемся найти пользователя с указанными данными
            $user = $repos->getUser($email, $password);
            // Если все получилось, то сохраняем в сесии его основные данные
            $_SESSION['user'] = [
                'id' => $user->getId(),
                'name' => $user->getUserName(),
                'email' => $user->getEmail()
            ];
            // После успешной авторизации убираем ошибки и заполненные поля в форме
            unset($_SESSION['errors']);
            unset($_SESSION['user_data']);
            // Перенаправляем на главную страницу
            Router::redirect('/');
        } catch (Exception $e) {
            // Ошибка может возникнуть при нахождении пользователя
            // Сохраняем в сессию сообщение об ошибках введенных данных
            $_SESSION['errors'] = [
                'email' => 'Неправильная почта',
                'password' => 'Неправильный пароль'
            ];
            // Сохраняем введенные поля в сессии
            $_SESSION['user_data'] = $data;
            // Перенаправляем обратно на страницу авторизации
            Router::redirect('/login');
        }
    }

    /**
     * Обработчик POST запроса на выход из аккаунта
     */
    public function logout()
    {
        // Удаляем из текущей сессии все данные пользователя
        unset($_SESSION['user']);
        // Перенаправляем на страницу авторизации
        Router::redirect('/login');
    }
}
