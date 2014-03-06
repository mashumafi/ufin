<?php
	require_once '../lib/all.php';
	json_header();
	echo collections_create($_GET['title']);
?>