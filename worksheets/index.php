<?php
	// worksheets?
	// sskey: key/id of the spreadsheet
	// maybe more query params in future

	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->worksheet_feed($_GET['title'])->to_json();
?>
