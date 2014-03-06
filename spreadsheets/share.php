<?php
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->share($_GET['email'])->to_json();
?>
