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
				
				if($categoriesFetched !== NULL && sizeof($categoriesFetched) > 2)
				{
					$ourRentalObjectsHTMLContent = '<p class="fs-4 mb-3">Unser Verleihsortiment</p>';
					
					$ourRentalObjectsHTMLContent .= '<p class="fs-6 mb-3">Zahlreiche Mietartikel aus folgenden Kategorien bieten wir an 😊</p>';
					
					for($i = 0; $i < sizeof($categoriesFetched); $i++)
					{
						$ourRentalObjectsHTMLContent .= '<p class="fs-6 mb-3">• '.$categoriesFetched[$i]['Title'].'</p>';
					}
					
					$ourRentalObjectsHTMLContent .= '<a class="btn btn-primary mb-3" href="'.DOMAIN.'/search">Jetzt stöbern und anfragen</a>';
				}
				else
				{
					$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', NULL, NULL);
					
					// Is there no article AND no category online? (then VERY likely website was freshly installed)
					if($articleQuery === NULL || sizeof($articleQuery) === 0)
					{
						$ourRentalObjectsHTMLContent = <<<STYLE
						
						STYLE;
						
						$ourRentalObjectsHTMLContent .= '<div class="row">';
						$ourRentalObjectsHTMLContent .= '<div class="col-12">';
						$ourRentalObjectsHTMLContent .= '<span class="fs-4">Mietartikel im Aufbau&nbsp;</span>';
						$ourRentalObjectsHTMLContent .= '<img style="vertical-align: text-top;" class="d-inline-block gear-spin" width="17" height="17" src="'.DOMAIN.'/img/symbol/gear-fill.svg" alt="gear-fill.svg">';
						$ourRentalObjectsHTMLContent .= '</div>';
						$ourRentalObjectsHTMLContent .= '</div>';
						
						$ourRentalObjectsHTMLContent .= '<div class="row mt-3">';
						$ourRentalObjectsHTMLContent .= '<div class="col-12">';
						$ourRentalObjectsHTMLContent .= '<p class="">Sorry, aber derzeit scheint der Inhaber noch keine Mietartikel oder Kategorien online gestellt zu haben.</p>';
						$ourRentalObjectsHTMLContent .= '<p class="">Bitte schauen Sie später noch einmal vorbei.</p>';
						$ourRentalObjectsHTMLContent .= '<p class="">Hier sollten bald zahlreiche Artikel erscheinen 😊</p>';
						$ourRentalObjectsHTMLContent .= '</div>';
						$ourRentalObjectsHTMLContent .= '</div>';
					}
					else // No category was created for any article BUT at least one article is online (so give users the opportunity to go there and see)
					{
						$ourRentalObjectsHTMLContent = '<p class="fs-4 mb-3">Unser Verleihsortiment</p>';
					
						$ourRentalObjectsHTMLContent .= '<p class="fs-6 mb-4">Klicken Sie auf die Schaltfläche, um unsere Mietartikel zu erkunden 😊</p>';
						
						$ourRentalObjectsHTMLContent .= '<a class="btn btn-primary mb-3" href="'.DOMAIN.'/search">Jetzt stöbern und anfragen</a>';
					}
				}

                $essentials -> getViewFactory() -> echoFeed(NULL, $ourRentalObjectsHTMLContent);
			?>
		</div>
		<?php
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>