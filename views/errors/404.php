<?php

use App\Services\View;

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ошибка! Страница не найдена!</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href=<?php echo View::includeStyle(); ?>>
</head>
<body class="bg-color">

<?php View::getPartByName('navbar'); ?>

<div class="container">
    <h1 class="text-center">404 - страница не найдена!</h1>
</div>