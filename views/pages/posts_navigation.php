<?php

use App\Repository\PosterRepository;
use App\Services\View;

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная страница</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href=<?php echo View::includeStyle(); ?>>
</head>
<body class="bg-color">

<?php
View::getPartByName('navbar');
$postRepos = new PosterRepository();
$posters = $postRepos->getAllPosts();
?>

<div class="container">
    <h2 class="home__posts-title">Все посты </h2>
    <table class="home__posts-table">
        <tbody>
        <th class="home__posts-table-header">Название поста</th>
        <th class="home__posts-table-header">Автор поста</th>
        <th class="home__posts-table-header">Дата загрузки</th>

        <?php foreach ($posters as $post) : ?>
            <tr>
                <td class="home__posts-table-cell">
                    <a href=<?= $post->getPostUrl() ?>><?= $post->getName() ?></a>
                </td>
                <td class="home__posts-table-cell"><?= $post->getUserName() ?></td>
                <td class="home__posts-table-cell"><?= $post->getCreateDate() ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <div class="row button-line">
        <button type="submit">
            Назад
        </button>
        <button type="submit">
            Далее
        </button>
    </div>
</div>
