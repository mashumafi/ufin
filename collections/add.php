<?php
	require_once '../lib/all.php';
	json_header();
	echo collections_add($_GET['id'], $_GET['sskey']);
?>