<?php
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->deny($_GET['email'])->to_json();
?>
