<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';
    $essentials = new Essentials();

	// See if logged in yet.
	if(isset($_SESSION['username']) && !empty($_SESSION['username']))
	{
		header('Location: '.DOMAIN.'/home');
		exit(0);
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load head-tag information and its configuration.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body>
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
		?>
		<div class="container-fluid">
		<?php
			$loginHTMLContent = 
			<<<HTML
			
			<span class="fs-2">Einloggen</span>
			<br>
			<br>
			<form action="login.php" method="post">
				<div class="mb-3">
					<label for="usernameField" class="form-label">Nutzername</label>
					<input type="text" name="username" class="form-control" id="usernameField">
				</div>
				<br>
				<div class="mb-3">
					<label for="passwordField" class="form-label">Passwort</label>
					<input type="password" name="password" class="form-control" id="passwordField">
				</div>
				<br>
				<button type="submit" class="btn btn-primary">Einloggen</button>
			</form>
			<p class="text-danger mt-3">
			
			HTML;
			
			if(isset($_GET['issue']))
			{
				switch($_GET['issue'])
				{
					case 'bothFieldsEmpty':
					{
						$loginHTMLContent .= 'Felder dürfen nicht leer sein';
					}
					break;
					
					case 'usernameEmpty':
					{
						$loginHTMLContent .= 'Benutzername fehlt';
					}
					break;
					
					case 'passwordEmpty':
					{
						$loginHTMLContent .= 'Passwort fehlt';
					}
					break;

					case 'notRegistered':
					{
						$loginHTMLContent .= 'Benutzername oder Passwort falsch'; // don't tell the exact reason because of brute-force risk
					}
					break;
					
					case 'passwordWrong':
					{
						$loginHTMLContent .= 'Benutzername oder Passwort falsch'; // don't tell the exact reason because of of brute-force risk
					}
					break;
					
					case 'insufficientPermission':
					{
						$loginHTMLContent .= 'Zugriff verweigert';
					}
					break;
					
					case 'pleaseLoginFirst':
					{
						$loginHTMLContent .= 'Bitte erst einloggen!';
					}
					break;
				}
			}
		?>
		</p>
		<?php
			$essentials -> getViewFactory() -> echoFeed(NULL, $loginHTMLContent);
		?>
		</div>
		<?php
			// Post-initialization and loading of necessary content after main part.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
