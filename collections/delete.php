<?php
	require_once '../lib/all.php';
	json_header();
	echo collections_delete($_GET['id']);
?>