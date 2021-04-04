<?php


namespace App\Controller;

use App\Services\Router;

class HomeController
{
    /**
     * Обработчик GET запроса на получение доступа к главной странице
     */
    public function loadHomePage()
    {
        // Удаляем ошибки и поля появившиеся на этапе заполнения полей в форме
        unset($_SESSION['errors']);
        unset($_SESSION['user_data']);
        // Перенаправляем на главную страницу
        Router::redirect('/home');
    }
}
