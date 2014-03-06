<?php
	require_once '../lib/all.php';
	json_header();
	echo collections_feed($$_GET['title']);
?>
