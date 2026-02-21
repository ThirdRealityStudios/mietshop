<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

	$essentials = new Essentials();
	$essentials -> getDatabaseManager() -> perform('ALTER DATABASE '.DB_NAME.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
	
	// Check if visitor is permitted to access the home page.
	if(!isset($_SESSION['username']))
	{
		header('Location: '.DOMAIN.'/login?issue=pleaseLoginFirst');
		exit(0);
	}
	
	if(empty($_SESSION['username']))
	{
		header('Location: '.DOMAIN.'/login?issue=sessionCleared');
		exit(0);
	}
?>
