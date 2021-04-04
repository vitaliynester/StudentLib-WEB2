<?php


namespace App\Services;

class View
{
    /**
     * Идентификатор поста при открытии страницы с подробной информацией о посте
     * @var int (идентификатор открываемого поста)
     */
    private static int $openedPostId;

    /**
     * Возвращает компонент верстки (header, footer, navbar и т.д.)
     * @param string $componentName (название компонента верстки)
     */
    public static function getPartByName(string $componentName)
    {
        require_once "views/components/" . $componentName . ".php";
    }

    /**
     * Подключить стили с помощью глобального пути
     * @return string (глобальный путь до .css файла)
     */
    public static function includeStyle(): string
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/assets/style/style.css';
    }

    /**
     * Получить глобальный путь до изображения (.svg)
     * @param string $name (название открываемого файла)
     * @return string (полученный глобальный путь до изображений)
     */
    public static function includeImage(string $name): string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . '/assets/images/' . $name . '.svg';
    }

    /**
     * Получить глобальный путь до скриптового файла (.js)
     * @param string $name (название скрипта)
     * @return string (полученный глобальный путь до скрипта)
     */
    public static function includeScript(string $name): string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . '/assets/js/' . $name . '.js';
    }

    /**
     * Проверить авторизацию пользователя (если авторизован, то переход)
     * @param string $redirectPage (страница на которую нужно перейти при активной авторизации)
     */
    public static function checkIfLogin(string $redirectPage)
    {
        if (isset($_SESSION['user'])) {
            Router::redirect($redirectPage);
            die();
        }
    }

    /**
     * Проверить авторизацию пользователя (если неавторизованный, то переход)
     * @param string $redirectPage (страница на которую нужно перейти при неактивной авторизации)
     */
    public static function checkIfNotLogin(string $redirectPage)
    {
        if (!isset($_SESSION['user'])) {
            Router::redirect($redirectPage);
            die();
        }
    }

    /**
     * Получить идентификатор открываемого поста
     * @return int (идентификатор поста)
     */
    public static function getOpenedPostId(): int
    {
        return self::$openedPostId;
    }

    /**
     * Установить идентификатор открываемого поста
     * @param int $openedPostId (новый идентификатор поста)
     */
    public static function setOpenedPostId(int $openedPostId): void
    {
        self::$openedPostId = $openedPostId;
    }
}
