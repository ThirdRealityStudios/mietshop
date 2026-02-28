<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    $essentials = new Essentials();
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load HTML tag information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
		<style>
			@keyframes spin
			{
				from
				{
					transform: rotate(0deg);
				}
				to
				{
					transform: rotate(360deg);
				}
			}

			.gear-spin
			{
				display: inline-block;
				animation: spin 1.4s linear infinite;
			}
		</style>
    </head>
	<body class="bg-body-secondary">
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
		?>
		<div class="container-fluid">
			<?php
				// Generate a nice Bootstrap carousel / image gallery.
				echo $essentials -> getViewFactory() -> genCarouselForBeginning();
				
				$firstFeedHTMLContent = '<p class="fs-4 mb-3">Warum bei uns mieten?</p><p class="fs-6">Bau- und Arbeitsgeräte<br><br>(1) zu einem <b>perfekten Preis-Leistungsverhältnis</b>.<br><br>(2) von einem <b>kompetenten Service mit Erfahrung</b>.<br><br>(3) in einem <b>Umkreis von 55 km</b> rund um '.COMPANY_PLACE.'</p>';

                $essentials -> getViewFactory() -> echoFeed(NULL, $firstFeedHTMLContent);

				$categoriesFetched = $essentials -> getDatabaseManager() -> select("*", "Category", NULL, NULL);
				

			?>
		</div>
		<?php
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>