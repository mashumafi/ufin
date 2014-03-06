<?php
	// rows and columns indexing begins at 1
	// cells.php?
	// sskey: key/id of the spreadsheet
	// wsid: key/id of the worksheet
	// minc: min column query (inclusive)
	// maxc: max column query (inclusive)
	// minr: min row query (inclusive)
	// maxr: max row query (inclusive)
	
	require_once '../lib/all.php';
	json_header();
	echo Spreadsheet::single($_GET['ssid'])->worksheet($_GET['wsid'])->cell_feed(@$_GET['mincol'], @$_GET['minrow'], @$_GET['maxcol'], @$_GET['maxrow'])->to_json();
?>
