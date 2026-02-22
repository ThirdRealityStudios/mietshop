<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load head-tag information and its configuration.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body class="bg-body-secondary">
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
		?>
		<div class="container-fluid">
		<?php
			$uploadHTMLContent = '<span class="fs-4">Artikel inserieren:</span><br><br>';
			
			$uploadHTMLContent .= $essentials -> getDatabaseManager() -> getArticleManager() -> genHTMLForm(true, null);
	
			$essentials -> getViewFactory() -> echoFeed(NULL, $uploadHTMLContent);
		?>
		</div>
		<?php
			// Post-initialization and loading of necessary content after main part.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
