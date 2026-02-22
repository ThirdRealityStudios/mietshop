<?php	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    $essentials = new Essentials();

	// Only execute login credential check below if user not logged in yet.
	if(!isset($_SESSION['username']) || empty($_SESSION['username']))
	{
		// At least one field not sent (technical issue).
		if(!isset($_POST['username']) || !isset($_POST['password']))
		{
			if(!isset($_POST['username']) && !isset($_POST['password']))
			{
				header('Location: index.php?issue=bothFieldsNotSent');
			}
			else if(!isset($_POST['username']))
			{
				header('Location: index.php?issue=usernameNotSent');
			}
			else if(!isset($_POST['password']))
			{
				header('Location: index.php?issue=passwordNotSent');
			}
			
			exit(0);
		}
		
		// At least one field not filled in (most likely user issue).
		if(empty($_POST['username']) || empty($_POST['password']))
		{
			// Both fields empty.
			if(empty($_POST['username']) && empty($_POST['password']))
			{
				header('Location: index.php?issue=bothFieldsEmpty');
			}
			// Username field empty.
			else if(empty($_POST['username']))
			{
				header('Location: index.php?issue=usernameEmpty');
			}
			// Password field empty.
			else if(empty($_POST['password']))
			{
				header('Location: index.php?issue=passwordEmpty');
			}
			
			exit(0);
		}
		
		$password = $essentials -> getDatabaseManager() -> getAccountManager() -> getPassword($_POST['username']);
		
		// User is not registered.
		if($password == null)
		{
			header('Location: '.DOMAIN.'/login?issue=notRegistered');
			exit(0);
		}
	}
	
	$loginStatus = $essentials -> getDatabaseManager() -> getAccountManager() -> login($_POST['username'], $_POST['password']);
	
	// Login successful
	if($loginStatus == 0)
	{
		// Remember the user which is logged in.
		$_SESSION['username'] = $_POST['username'];
		
		header('Location: '.DOMAIN.'/home');
		exit(0);
	}
	
	if($loginStatus != 0) // Login credentials wrong (password).
	{
		header('Location: index.php?issue=passwordWrong');
		exit(0);
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<title>Redirecting..</title>
	</head>
	<body></body>
</html>