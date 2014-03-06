<?php
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->
		create_worksheet($_GET['name'], @$_GET['rows'], @$_GET['cols'])->
		to_json();;
?>
