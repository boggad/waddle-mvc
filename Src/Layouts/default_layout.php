<html>
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
