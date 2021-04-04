<?php

use App\Services\View;

View::checkIfLogin('/');
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo View::includeStyle(); ?>>
</head>
<body class="auth">

<?php
View::getPartByName('navbar');
?>

<div class="container mt-4">
    <h2 class="auth__title">Регистрация</h2>
    <form method="post" action="/auth/register">

        <?php if (isset($_SESSION['errors']['last_name'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="last_name">Фамилия</label>
                <input type="text" class="form-control is-invalid" name="last_name" id="last_name" placeholder="Иванов"
                       value="<?php echo $_SESSION['user_data']['last_name'] ?>" required>
                <span class="text-danger"><?php echo $_SESSION['errors']['last_name'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['last_name'])) : ?>
            <div class="form-group">
                <label for="last_name">Фамилия</label>
                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Иванов"
                       value="<?php echo $_SESSION['user_data']['last_name'] ?>" required>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="last_name">Фамилия</label>
                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Иванов" required>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']['first_name'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="first_name">Имя</label>
                <input type="text" class="form-control is-invalid" name="first_name" id="first_name" placeholder="Иван"
                       value="<?php echo $_SESSION['user_data']['first_name'] ?>" required>
                <span class="text-danger"><?php echo $_SESSION['errors']['first_name'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['first_name'])) : ?>
            <div class="form-group">
                <label for="first_name">Имя</label>
                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Иван"
                       value="<?php echo $_SESSION['user_data']['first_name'] ?>" required>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="first_name">Имя</label>
                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Иван" required>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']['patronymic'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="patronymic">Отчество</label>
                <input type="text" class="form-control is-invalid" name="patronymic" id="patronymic"
                       placeholder="Иванович"
                       value="<?php echo $_SESSION['user_data']['patronymic'] ?>">
                <span class="text-danger"><?php echo $_SESSION['errors']['patronymic'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['patronymic'])) : ?>
            <div class="form-group">
                <label for="patronymic">Отчество</label>
                <input type="text" class="form-control" name="patronymic" id="patronymic" placeholder="Иванович"
                       value="<?php echo $_SESSION['user_data']['patronymic'] ?>">
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="patronymic">Отчество</label>
                <input type="text" class="form-control" name="patronymic" id="patronymic" placeholder="Иванович"
                >
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']['email'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="email">Электронная почта</label>
                <input type="email" class="form-control is-invalid" name="email" id="email"
                       placeholder="example@mail.ru"
                       value="<?php echo $_SESSION['user_data']['email'] ?>" required>
                <span class="text-danger"><?php echo $_SESSION['errors']['email'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['email'])) : ?>
            <div class="form-group">
                <label for="email">Электронная почта</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="example@mail.ru"
                       value="<?php echo $_SESSION['user_data']['email'] ?>" required>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="email">Электронная почта</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="example@mail.ru"
                       required>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']['password'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="password">Пароль</label>
                <input type="password" class="form-control is-invalid" name="password" id="password"
                       placeholder="******"
                       value="<?php echo $_SESSION['user_data']['password'] ?>" required>
                <span class="text-danger"><?php echo $_SESSION['errors']['password'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['password'])) : ?>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="******"
                       value="<?php echo $_SESSION['user_data']['password'] ?>" required>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="******"
                       required>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']['password_confirm'])) : ?>
            <div class="form-group">
                <label class="text-danger" for="password_confirm">Повторите пароль</label>
                <input type="password" class="form-control is-invalid" name="password_confirm" id="password_confirm"
                       placeholder="******"
                       value="<?php echo $_SESSION['user_data']['password_confirm'] ?>" required>
                <span class="text-danger"><?php echo $_SESSION['errors']['password_confirm'] ?></span>
            </div>
        <?php elseif (isset($_SESSION['user_data']['password_confirm'])) : ?>
            <div class="form-group">
                <label for="password_confirm">Повторите пароль</label>
                <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                       placeholder="******"
                       value="<?php echo $_SESSION['user_data']['password_confirm'] ?>" required>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="password_confirm">Повторите пароль</label>
                <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                       placeholder="******"
                       required>
            </div>
        <?php endif; ?>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="personal_data" required>
            <div>
                <label class="auth__helped-label" for="personal_data">Я согласен с использованием </label>
                <a href="https://www.youtube.com/watch?v=NeNtRWaPT38">моих персональных данных</a>
            </div>
        </div>

        <button type="submit" class="auth__confirm-btn">Зарегистрироваться</button>
    </form>
</div>
