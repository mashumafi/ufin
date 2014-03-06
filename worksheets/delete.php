<?php
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->worksheet($_GET['wsid'])->delete();
?>
