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
    <title>Авторизация</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo View::includeStyle(); ?>>
</head>
<body class="auth">

<?php View::getPartByName('navbar'); ?>

<div class="container mt-4">
    <h2 class="auth__title">Авторизация</h2>
    <form method="post" action="/auth/login">

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
                <input type="email" class="form-control" name="email" id="email" placeholder="example@mail.ru" required>
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
                <input type="password" class="form-control" name="password" id="password" placeholder="******" required>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <span class="auth__helped-label">Нет аккаунта?</span>
            <a href="/register"> Зарегистрируйтесь!</a>
        </div>
        <button type="submit" class="auth__confirm-btn">Войти</button>
    </form>
</div>

</body>
</html>