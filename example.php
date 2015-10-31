<?php
	include 'src/Librator.php';
	$librator = new Visavi\Librator('library.txt');
	$librator->setBreak('<br>');
	$text = $librator->read(20, 'lines');
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

		Страница: <?= $page ?><br />
		<?= $text ?>
	</body>
</html>
