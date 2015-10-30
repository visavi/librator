<?php
include 'src/Librator.php';

$librator = new Visavi\Librator('library.txt');
$librator->setBreak('<br>');
$librator->read(20);
