<?php

use App\Services\View;

?>
<nav class="navbar navbar-expand-lg navbar-expand-md navbar-expand-sm navbar-light bg-color">
    <div class="container">
        <a href="/home">
            <img class="home-icon" src=<?php echo View::includeImage('home'); ?> alt="Главная страница">
        </a>

        <input class="form-control input-lg input-header" type="search" placeholder="Найти пост...">

        <?php if (isset($_SESSION['user'])) : ?>
            <form action="/create-post" method="post">
                <div class="exit-btn">
                    <button class="btn exit-btn" type="submit">
                        <img src=<?php echo View::includeImage('add_post_icon'); ?> alt="Добавить_новый_пост">
                    </button>
                </div>
            </form>
            <div class="dropdown">
                <span class="exit-btn__name">
                    <?php echo $_SESSION['user']['name'] ?>
                </span>
                <div class="dropdown-content">
                    <a><?php echo $_SESSION['user']['name'] ?></a>
                    <a><?php echo $_SESSION['user']['email'] ?></a>
                    <a>
                        <form action="/auth/logout" method="post">
                            <button class="exit-btn exit-btn-button" type="submit">Выйти</button>
                        </form>
                    </a>
                </div>
            </div>

        <?php else : ?>
            <button class="btn p-0" type="submit">
                <div class="login-btn">
                    <a class="login-btn" href="/login">
                        <span>Войти </span>
                        <img src="assets/images/enter_button.svg" alt="Главная страница">
                    </a>
                </div>
            </button>
        <?php endif; ?>
    </div>
</nav>