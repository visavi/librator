<?php
	require 'vendor/autoload.php';

	$librator = new Visavi\Librator('library.txt');
	$text = $librator->read(20);
	//$text = $librator->read(2000, 'chars');
	//$text = $librator->read(300, 'words');
	$page = $librator->currentPage();
	$title = $librator->getTitle();
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?= $title ?></title>
		<link href="/src/css/style.css" rel="stylesheet">
	</head>
	<body>
		<h1><?= $title ?></h1>

		<p class="current-page">Страница: <?= $page ?></p>
		<?= $text ?>
	</body>
</html>
