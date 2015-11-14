<!DOCTYPE html>
<!--[if lt IE 7]>
<html lang="ru" class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]>
<html lang="ru" class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]>
<html lang="ru" class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title><?=$app->getSiteTitle()?></title>

    <link rel="shortcut icon" href="/favicon.png" />
    <link rel="stylesheet" href="<?= $app->asset('css/main.css') ?>"/>

</head>
<body>

<?php
/**
 * @var $content
 */

include_once $content;

?>

</body>
</html>