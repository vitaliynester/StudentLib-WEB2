<?php

use App\Repository\FileRepository;
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
    <title>Детальная страница поста</title>
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
$postRepo = new PosterRepository();
$filesRepo = new FileRepository();
$detailPost = $postRepo->getPosterById($_SESSION['detail_post']);
$files = $filesRepo->getFilesFromPost($detailPost->getId());
?>

<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-3 detail-post">
            <div class="detail-post__information">
                <div class="row detail-post__information-card">
                    <div class="detail-post__information-card-pre">
                        Имя:
                    </div>
                    <div class="detail-post__information-card-value">
                        <?= $detailPost->getName() ?>
                    </div>
                </div>
                <div class="row detail-post__information-card">
                    <div class="detail-post__information-card-pre">
                        Дата:
                    </div>
                    <div class="detail-post__information-card-value">
                        <?= $detailPost->getCreateDate() ?>
                    </div>
                </div>
                <div class="row detail-post__information-card">
                    <div class="detail-post__information-card-pre">
                        Автор:
                    </div>
                    <div class="detail-post__information-card-value">
                        <?= $detailPost->getUserName() ?>
                    </div>
                </div>
            </div>
            <div class="detail-post__files">
                <?php foreach ($files as $file) : ?>
                    <li>
                        <a download="<?php echo $file->getFileSrcName() ?>"
                           href=<?php echo $file->getDownloadUrl(); ?>>
                            <?php echo $file->getFileSrcName(); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-9 col-md-9 detail-post__description">
        <span>
            <?php echo $detailPost->getDescription(); ?>
        </span>
        </div>
    </div>
</div>
</body>