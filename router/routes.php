<?php

use App\Controller\AuthorizationController;
use App\Controller\HomeController;
use App\Controller\PosterController;
use App\Services\Router;

// Все доступные страницы для перехода
Router::page("/login", "login");
Router::page("/register", "register");
Router::page("/home", "home");
Router::page("/create-post", "add_post");
Router::page("/php-info", "php_info");
Router::page('/detail-post', "detail_post");
Router::page('/all-posts', "posts_navigation");

// Все доступные методы для обработки
Router::action('/', HomeController::class, "loadHomePage", "GET");
Router::action("/auth/register", AuthorizationController::class, "register", "POST", true);
Router::action("/auth/login", AuthorizationController::class, "login", "POST", true);
Router::action("/auth/logout", AuthorizationController::class, "logout", "POST", true);
Router::action('/post/create', PosterController::class, "create", "POST", true, true);
Router::action('/post/{id}', PosterController::class, "openPost", "GET");
Router::action('/posts/{id}', PosterController::class, "navigationPosts", "GET");

// Подключаем все вышеперечисленные методы и страницы
Router::enable();
