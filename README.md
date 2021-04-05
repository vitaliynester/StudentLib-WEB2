## Лабораторная работа №2 по дисциплине "Технологии WEB-приложений"

## Вариант 1 (Студенческий файлообменник)

Разработать студенческий файлообменник с учебными материалами по предметам.

На всех страницах в шапке сайта для неавторизованных пользователей выводить ссылки на авторизацию и регистрацию, для
авторизованных — приветствие, кнопку “Выход” и ссылку на добавление поста.

На главной странице выводятся посты, отсортированные по дате добавления. У каждого поста выводится название (ссылка на
страницу поста), дата добавления, имя автора.

На странице поста выводится информация о посте:
название дата добавления текст описания имя автора список прикрепленных файлов (ссылки на скачивание)

На странице добавления поста располагается форма с полями название описание файлы (множественное поле. Допустимы файлы
типов: zip, doc, docx, xls, xlsx, pdf, jpg, png)
Все поля обязательные. Если какие-то из введенных значений невалидны, сохранения в БД не происходит, на форме выводятся
сообщения об ошибках. Заполненные пользователем текстовые поля не сбрасываются.

После успешной отправки выполнять редирект на страницу созданного поста.

Страница доступна только авторизованным пользователи.

## Описание проекта

Проект был написан на PHP `7.4.16`.

В виде СУБД использовался PostgreSQL `13.2`.

SQL скрипт для создания структуры БД находится [здесь](), а также продублирован далее:

```sql
/*==============================================================*/
/* Table: "USER"                                                */
/*==============================================================*/
create table user_profile
(
    USER_ID    SERIAL PRIMARY KEY,
    LAST_NAME  VARCHAR(255)        not null,
    FIRST_NAME VARCHAR(255)        not null,
    PATRONYMIC VARCHAR(255)        null,
    EMAIL      VARCHAR(255) UNIQUE not null,
    PASSWORD   VARCHAR(255)        not null
);

/*==============================================================*/
/* Index: USER_PK                                               */
/*==============================================================*/
create unique index USER_PK on user_profile (USER_ID);

/*==============================================================*/
/* Table: POST                                                  */
/*==============================================================*/
create table POST
(
    POST_ID          SERIAL primary key,
    USER_ID          integer references user_profile (USER_ID) not null,
    POST_NAME        VARCHAR(255)                              not null,
    POST_CREATE_DATE TIMESTAMP                                 not null,
    POST_EDIT_DATE   TIMESTAMP                                 not null,
    POST_DESCRIPTION VARCHAR(5000)                             not null
);

/*==============================================================*/
/* Index: POST_PK                                               */
/*==============================================================*/
create unique index POST_PK on POST (POST_ID);

/*==============================================================*/
/* Table: FILE                                                  */
/*==============================================================*/
create table FILE
(
    FILE_ID             SERIAL primary key,
    POST_ID             integer references POST (POST_ID) not null,
    FILE_NAME           VARCHAR(50)                       not null,
    FILE_TYPE           VARCHAR(50)                       not null,
    FILE_PATH           VARCHAR(120)                      not null,
    FILE_SRC_NAME       VARCHAR(50)                       not null,
    FILE_DOWNLOAD_COUNT integer                           not null default 0
);

/*==============================================================*/
/* Index: FILE_PK                                               */
/*==============================================================*/
create unique index FILE_PK on FILE (FILE_ID);

/*==============================================================*/
/* Index: INCLUDE_FK                                            */
/*==============================================================*/
create index INCLUDE_FK on FILE (POST_ID);

/*==============================================================*/
/* Index: CREATE_FK                                             */
/*==============================================================*/
create index CREATE_FK on POST (USER_ID);

alter table FILE
    add constraint FK_FILE_INCLUDE_POST foreign key (POST_ID)
        references POST (POST_ID)
        on delete cascade on update cascade;

alter table POST
    add constraint FK_POST_CREATE_USER foreign key (USER_ID)
        references user_profile (USER_ID)
        on delete restrict on update cascade;
```

Также, для удобного разворачивания проекта был составлен `docker-compose.yaml` файл. Он включает в себя 4 сервиса:
postgres (для БД), nginx (проксирование трафика), php-fpm (сервис проекта) и adminer (веб-версия визуализации БД).

## Разворачивание проекта без использования Docker

Для разворачивания проекта необходимо выполнить следующие действия:

1. Скачать данный репозиторий к себе на компьютер. Это можно сделать с помощью следующей команды:

```bash
git clone ...
```

2. Теперь необходимо установить все зависимости проекта, для этого необходимо воспользоваться следующей командой:

```bash
composer install
```

3. После этого необходимо настроить Nginx. Готовый конфиг находится [здесь](), а также представлен далее:

```nginx
server {
    client_max_body_size 300M;
    listen 80;
    server_name localhost;
    root /var/www/student_lib;

    location / {
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /index.php?q=$1 last;
    }

    location ~ \.php$ {
        try_files $uri @rewriteapp;
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
    }

    error_log /var/log/nginx/student_lib_error.log;
    access_log /var/log/nginx/student_lib_access.log;
}
```

## Разворачивание проекта с использованием Docker

1. Скачать данный репозиторий к себе на компьютер. Это можно сделать с помощью следующей команды:

```bash
git clone ...
```

2. Перейти в каталог `./docker` и выполнить команду `docker compose up -d`. После этого проект будет доступен на 80
   порту.

## Структура проекта

Проект представляет собой следующую структуру:

```bash
├── README.md
├── app
│   ├── Controller
│   │   ├── AuthorizationController.php
│   │   ├── HomeController.php
│   │   └── PosterController.php
│   ├── Entity
│   │   ├── BaseEntity.php
│   │   ├── FileEntity.php
│   │   ├── PosterEntity.php
│   │   └── UserEntity.php
│   ├── Helper
│   │   ├── DbConnection.php
│   │   ├── files_extension_constants.php
│   │   └── files_type_constants.php
│   ├── Repository
│   │   ├── BaseRepository.php
│   │   ├── FileRepository.php
│   │   ├── PosterRepository.php
│   │   └── UserRepository.php
│   ├── Services
│   │   ├── Router.php
│   │   └── View.php
│   └── Validator
│       ├── BaseValidator.php
│       ├── FileValidator.php
│       ├── PosterValidator.php
│       └── UserValidator.php
├── assets
│   ├── images
│   │   ├── add_files_icon.svg
│   │   ├── add_post_icon.svg
│   │   ├── all_posts_icon.svg
│   │   ├── archive_icon.svg
│   │   ├── document_icon.svg
│   │   ├── enter_button.svg
│   │   ├── home.svg
│   │   ├── images_icon.svg
│   │   ├── other_icon.svg
│   │   └── post_preview_icon.svg
│   ├── js
│   │   └── create-post-files.js
│   └── style
│       ├── _auth.scss
│       ├── _create-post.scss
│       ├── _detail-post.scss
│       ├── _globals.scss
│       ├── _header.scss
│       ├── _home.scss
│       ├── _posts-pagination.scss
│       ├── _variables.scss
│       ├── style.css
│       ├── style.css.map
│       └── style.scss
├── composer.json
├── composer.lock
├── docker
│   ├── docker-compose.yaml
│   ├── nginx
│   │   ├── Dockerfile
│   │   └── default.conf
│   ├── php-fpm
│   │   ├── Dockerfile
│   │   └── php.ini
│   └── postgres
│       ├── 1-init.sql
│       ├── Dockerfile
│       └── database.env
├── index.php
├── router
│   └── routes.php
├── uploads
└── views
    ├── components
    │   └── navbar.php
    ├── errors
    │   ├── 404.php
    │   └── 500.php
    └── pages
        ├── add_post.php
        ├── detail_post.php
        ├── home.php
        ├── login.php
        ├── php_info.php
        ├── posts_navigation.php
        └── register.php

21 каталогов, 65 файлов
```

- `app` хранит в себе основную бизнес логику приложения
    - `Controller` - все контроллеры приложения. Обрабатывают данные перед вызовом `View`.
    - `Entity` - все сущности приложения. Представляют собой классы с необходимыми полями и методами, например:
      сохранение записи в базе данных.
    - `Helper` - сущность для работы с БД и основные параметры для обработки файловых типов.
    - `Repostitory` - все репозитории приложения. Представляют собой классы, которые способны выдавать данные по
      запросу.
    - `Services` - содержит класс для реализации роутинга и вспомогательный класс для отображения элементов страниц.
    - `Validator` - все классы валидаторов: пользовательский, для поста и для файла. Способен выдавать результат
      проверки и массив с описанием ошибок.
- `assets` хранит статические файлы приложения
    - `images` - изображения подключаемые в HTMl формах
    - `js` - JavaScript для использования на страницах HTML (обработка добавления файлов)
    - `style` - SCSS стили для HTML страниц
- `docker` хранит настройки и описания контейнеров для их запуска
    - `nginx` - настройка контейнера проксирования. Используется собственный конфигурационный файл.
    - `php-fpm` - настройка контейнера приложения. Используется собственный конфигурационный файл для ограничения
      размера загружаемых файлов
    - `postgres` - настройка контейнера базы данных. Используется собственный стартовый SQL скрипт для создания таблицы
      в БД.
- `router` - хранит настройки доступных страниц и GET/POST методов доступа.
- `uploads` - хранит загруженные файлы
- `views` - хранит верстку приложения
    - `components` - компоненты приложения (header, footer, navbar и т.д.)
    - `errors` - страницы с отображения соответствующих ошибок по статус-коду (404, 500 и т.д.)
    - `pages` - страницы приложения (главная, авторизация, регистрация, добавление поста и т.д.)
- `./.env` - файл с настройками виртуального окружения. В данном файле хранятся константы для подключения к БД с
  пользовательской стороны.
  

