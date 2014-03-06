<?php
	require_once '../lib/all.php';
	json_header();
	echo collections_remove($_GET['id'], $_GET['sskey']);
?>