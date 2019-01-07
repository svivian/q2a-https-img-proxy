<?php
/*
	Question2Answer HTTPS image proxy plugin
	Copyright (C) 2018 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

// prevent errors messing with image data
@ini_set('display_errors', 0);

if (!isset($_GET['img']) || !isset($_GET['hash'])) {
	exit;
}

$url = $_GET['img'];
$hash = $_GET['hash'];

