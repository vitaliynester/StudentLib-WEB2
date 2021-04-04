<?php

use App\Services\View;

View::checkIfNotLogin('/login');
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Создание нового поста</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo View::includeStyle(); ?>>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js"
            integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY="
            crossorigin="anonymous"></script>
</head>
<body class="bg-color">

<?php View::getPartByName('navbar'); ?>

<div class="container">
    <?php if (isset($_SESSION['errors']['file'])) : ?>
        <script type="text/javascript">
            $(document).ready(function () {
                var errorMsg = "<?php print($_SESSION['errors']['file']); ?>";
                alert(errorMsg);
            });
        </script>
    <?php endif; ?>
    <form action="post/create" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Название поста</label>
            <?php if (isset($_SESSION['user_data']['title'])) : ?>
                <input type="text" class="form-control" name="title" id="title"
                       placeholder="Диплом" required value=<?php echo $_SESSION['user_data']['title']; ?>>
            <?php else : ?>
                <input type="text" class="form-control" name="title" id="title"
                       placeholder="Диплом" required>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="description">Описание</label>
            <?php if (isset($_SESSION['user_data']['description'])) : ?>
                <textarea class="form-control" name="description" id="description"
                          required><?php echo $_SESSION['user_data']['description']; ?>
                </textarea>
            <?php else : ?>
                <textarea class="form-control" name="description" id="description" required></textarea>
            <?php endif; ?>
        </div>
        <div class="container row create-post">
            <div class="form-item">
                <input class="form-item__input" type="file" id="files" name="post_file[]" multiple required/>
                <label class="form-item__label" for="files">
                    <span>Загрузить файл(ы)</span>
                    <img src=<?php echo View::includeImage('add_files_icon'); ?> alt="Добавить файл">
                </label>
            </div>
            <button class="create-post__btn-publish" type="submit">
                Опубликовать
            </button>
        </div>
    </form>
</div>

<script src=<?php echo View::includeScript('create-post-files'); ?>></script>
</body>
</html>