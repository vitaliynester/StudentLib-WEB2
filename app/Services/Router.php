<?php

namespace App\Services;

class Router
{
    /**
     * @var array (Список доступных роутов)
     */
    private static array $routeList = [];

    /**
     * Добавление новой страницы в массив роутов
     * @param string $uri (путь по которому открывать страницу)
     * @param string $pageName (название файла для открытия страницы)
     */
    public static function page(string $uri, string $pageName)
    {
        self::$routeList[] = [
            "uri" => $uri,
            "page" => $pageName
        ];
    }

    /**
     * Добавление нового метода в массив роутов
     * @param string $uri (путь по которому необходимо обращаться)
     * @param string $controller (название контроллера для обработки запроса)
     * @param string $classMethod (название метода в указанном контроллере для обработки запроса)
     * @param string $method (вид запроса: GET, POST)
     * @param bool $formData (обрабатывать полученные данные из формы, True - да/False - нет)
     * @param bool $files (обрабатывать полученные файловые данные из формы, True - да/False - нет)
     */
    public static function action(
        string $uri,
        string $controller,
        string $classMethod,
        string $method,
        bool $formData = false,
        bool $files = false
    ) {
        self::$routeList[] = [
            "uri" => $uri,
            "class" => $controller,
            "class_method" => $classMethod,
            "method" => $method,
            "form_data" => $formData,
            "files" => $files
        ];
    }

    /**
     * Обработка входящих запросов
     */
    public static function enable()
    {
        $query = rtrim($_GET['q'], '/');
        $queryArray = self::convertQuery($query);
        foreach (self::$routeList as $route) {
            if ($route['uri'] == $queryArray['query']) {
                self::redirectMethod($route);
            } elseif (str_starts_with($route['uri'], $queryArray['query']) &&
                $queryArray['query'] != '/' && isset($queryArray['param'])) {
                self::redirectMethod($route, $queryArray['param']);
            }
        }
        self::errorPage(404);
    }

    /**
     * Преобразуем входной (сырой) запрос в удобное представление
     * @param string $srcQuery (запрос в адресной строке)
     * @return array (массив с запросом и параметром запроса)
     */
    private static function convertQuery(string $srcQuery): array
    {
        $navigationArray = array();
        if (empty($srcQuery)) {
            $query = '/';
        } else {
            $params = explode('/', ltrim($srcQuery, '/'));
            if ($params[0] == 'auth' || $params[1] == 'create') {
                $query = '/' . $params[0] . '/' . $params[1];
            } else {
                $query = '/' . $params[0];
                $param = isset($params[1]) ? $params[1] : "";
            }
        }
        $navigationArray['query'] = $query;
        $navigationArray['param'] = $param;
        return $navigationArray;
    }

    /**
     * Перенаправление запроса
     * @param array $route (запись из массива роутов)
     * @param string $param (параметры запроса, по умолчанию отсутствуют)
     */
    private static function redirectMethod(array $route, string $param = "")
    {
        $routeMethod = $route['method'];
        if ($routeMethod == "POST" && $_SERVER['REQUEST_METHOD'] == "POST") {
            // если POST метод, то
            self::redirectToPostMethod($route);
        } elseif ($routeMethod == "GET" && $_SERVER['REQUEST_METHOD'] == "GET") {
            // если GET метод, то
            self::redirectToGetMethod($route, $param);
        } else {
            // если это не метод, а просто страница
            self::redirectToPage($route);
        }
    }

    /**
     * Перенаправление запросов метода POST
     * @param array $route (запись из массива роутов)
     */
    private static function redirectToPostMethod(array $route)
    {
        // создаем новый экземпляр переданного класса
        $action = new $route['class'];
        // получаем метод указанный для данного контроллера
        $method = $route['class_method'];
        if ($route['form_data'] && $route['files']) {
            // если были переданы данные из формы и файлы
            $action->$method($_POST, $_FILES);
        } elseif ($route['form_data'] && !$route['files']) {
            // если были переданы данны только из формы
            $action->$method($_POST);
        } else {
            // если вообще не были переданы данные
            $action->$method();
        }
        die();
    }

    /**
     * Перенаправление запросов метода GET
     * @param array $route (запись из массива роутов)
     * @param string $param (параметр для GET запроса, по умолчанию отсутствует)
     */
    private static function redirectToGetMethod(array $route, string $param = "")
    {
        // создаем новый экземпляр указанного контроллера
        $action = new $route['class'];
        // получаем метод для указанного контроллера
        $method = $route['class_method'];
        if (!isset($param)) {
            // если параметр GET запроса не был передан
            $action->$method();
        } else {
            // если параметр GET запроса был передан
            $action->$method($param);
        }
        die();
    }

    /**
     * Перенаправить пользователя на конкретную страницу
     * @param array $route (страница для перенаправления)
     */
    private static function redirectToPage(array $route)
    {
        $filePath = "views/pages/" . $route['page'] . ".php";
        self::openFile($filePath);
    }

    /**
     * Метод для проверки существования открываемого файла с последующем его открытием
     * @param string $path
     */
    private static function openFile(string $path)
    {
        if (file_exists($path)) {
            require_once $path;
            die();
        }
    }

    /**
     * Открыть страницу с указанным статус-кодом ошибки
     * @param int $errorCode (статус код ошибки)
     */
    public static function errorPage(int $errorCode)
    {
        require "views/errors/" . $errorCode . ".php";
    }

    /**
     * Перенаправить пользователя на конкретный URL
     * @param string $uri (URL для перенаправления)
     */
    public static function redirect(string $uri)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $uri);
    }
}
