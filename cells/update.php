<?php
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->worksheet($_GET['wsid'])->cell_rc($_GET['row'], $_GET['col'])->update($_GET['input']);
?>
