<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    $essentials = new Essentials();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<div class="container-fluid mt-5 pb-5 bg-primary-subtle border-top border-3 border-primary">
	<?php
		$contactCardText = '<p class="fs-4">Kontakt:</p><br>'.COMPANY_STREET.'<br>'.COMPANY_ZIP.'&nbsp;'.COMPANY_PLACE.'<br><br>E-Mail:&nbsp;<a href="mailto:'.EMAIL_GENERAL.'" class="card-link" title="E-Mail an uns senden">'.EMAIL_GENERAL.'</a><br><br>Telefon:&nbsp;<a href="tel:'.COMPANY_PHONE_STATIONARY.'" class="card-link" title="Uns anrufen">'.COMPANY_PHONE_STATIONARY.'</a><br>Mobil:&nbsp;<a href="tel:'.COMPANY_PHONE_MOBILE.'" class="card-link" title="Uns anrufen">'.COMPANY_PHONE_MOBILE.'</a>';

		$essentials -> getViewFactory() -> echoFeed(NULL, $contactCardText);

		$socialMediaText = '';

		$disableAll = isset($_COOKIE["disableAll"]) && is_numeric($_COOKIE["disableAll"]) && $_COOKIE["disableAll"] == 1;
		$enableAll = isset($_COOKIE["enableAll"]) && is_numeric($_COOKIE["enableAll"]) && $_COOKIE["enableAll"] == 1;

		$enableKleinanzeigen = isset($_COOKIE["enableKleinanzeigen"]) && is_numeric($_COOKIE["enableKleinanzeigen"]) && $_COOKIE["enableKleinanzeigen"] == 1;

		if(!$disableAll)
		{
			// For best user-experience, the amount of social media links active is counted.
			$socialMediaCount = 0;

			if(strcmp(SOCIALMEDIA_KLEINANZEIGEN, '') != 0 && ($enableKleinanzeigen || $enableAll))
			{
				$socialMediaText .= '<a class="d-inline-block me-2 mb-2" href="'.SOCIALMEDIA_KLEINANZEIGEN.'"><img src="'.DOMAIN.'/img/logo/socialmedia/Kleinanzeigen.png" alt="Kleinanzeigen" title="Kleinanzeigen öffnen" height="50"></a>';
			
				$socialMediaCount++;
			}

			$enableInstagram = isset($_COOKIE["enableInstagram"]) && is_numeric($_COOKIE["enableInstagram"]) && $_COOKIE["enableInstagram"] == 1;

			if(strcmp(SOCIALMEDIA_INSTAGRAM, '') != 0 && ($enableInstagram || $enableAll))
			{
				$socialMediaText .= '<a class="d-inline-block me-2 mb-2" href="'.SOCIALMEDIA_INSTAGRAM.'"><img src="'.DOMAIN.'/img/logo/socialmedia/Instagram.png" alt="Instagram" title="Instagram öffnen" height="50"></a>';
			
				$socialMediaCount++;
			}
			
			$enableWhatsApp = isset($_COOKIE["enableWhatsApp"]) && is_numeric($_COOKIE["enableWhatsApp"]) && $_COOKIE["enableWhatsApp"] == 1;
			
			if(strcmp(SOCIALMEDIA_WHATSAPP, '') != 0 && ($enableWhatsApp || $enableAll))
			{
				$socialMediaText .= '<a class="d-inline-block me-2 mb-2" href="'.SOCIALMEDIA_WHATSAPP.'"><img src="'.DOMAIN.'/img/logo/socialmedia/WhatsApp.png" alt="WhatsApp" title="WhatsApp öffnen" height="50"></a>';
			
				$socialMediaCount++;	
			}
			
			$enableFacebook = isset($_COOKIE["enableFacebook"]) && is_numeric($_COOKIE["enableFacebook"]) && $_COOKIE["enableFacebook"] == 1;
			
			if(strcmp(SOCIALMEDIA_FACEBOOK, '') != 0 && ($enableFacebook || $enableAll))
			{
				$socialMediaText .= '<a class="d-inline-block me-2 mb-2" href="'.SOCIALMEDIA_FACEBOOK.'"><img src="'.DOMAIN.'/img/logo/socialmedia/Facebook.png" alt="Facebook" title="Facebook öffnen" height="50"></a>';
			
				$socialMediaCount++;
			}
			
			if($enableKleinanzeigen || $enableInstagram || $enableWhatsApp || $enableFacebook || $enableAll)
			{
				$optionalSocialMediaTitle = '';
			
				// It only makes sense to show a title when there are enough social media links.
				// For only one social media link it is not worth it.
				if($socialMediaCount > 1)
				{
					$optionalSocialMediaTitle = '<p class="fs-6">Alternativ können Sie uns auch auf den gängigen sozialen Medien ansprechen und einen Termin vereinbaren.</p><p class="fs-4">Social Media:</p>';
				}

				$essentials -> getViewFactory() -> echoFeed(NULL, $optionalSocialMediaTitle.$socialMediaText);
			}
			
			$enableGoogleMaps = isset($_COOKIE["enableGoogleMaps"]) && is_numeric($_COOKIE["enableGoogleMaps"]) && $_COOKIE["enableGoogleMaps"] == 1;
			
			if(strcmp(COMPANY_GOOGLE_MAPS_EMBED_LINK, '') != 0 && ($enableGoogleMaps || $enableAll))
			{
				$googleMapsHTMLFrame = '<iframe src="'.COMPANY_GOOGLE_MAPS_EMBED_LINK.'" allowfullscreen="" loading="lazy" class="rounded" style="width: 100%;" height="480"></iframe>';
				
				echo $essentials -> getViewFactory() -> preGenRow($googleMapsHTMLFrame, true, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE, FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE);
			}
			else
			{
				// fill up some space below if Google Maps should not be activated from the services to avoid white space below the footer.
				echo "<div style=\"height: 20vh;\"></div>";
			}
		}
		else
		{
			// also fill up some space below if really nothing should be activated.
			echo "<div style=\"height: 25vh;\"></div>";
		}

		$copyrightText = '<p class="mb-0 text-center">Copyright&nbsp;©&nbsp;'.COMPANY_NAME.'&nbsp;-&nbsp;Alle&nbsp;Rechte&nbsp;vorbehalten.</p>';
		
		echo $essentials -> getViewFactory() -> preGenRow($copyrightText, true, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE, FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE);
	?>
</div>