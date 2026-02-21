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
	</head>
	<body class="bg-body-secondary">
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
		?>
		<div class="container-fluid">
			<?php
				$imprintHTMLContent = '<span class="fs-6" style="white-space: pre-line;">';
				
				$imprintHTMLContent .= '<p class="fs-1">Impressum</p>';
				
				$imprintHTMLContent .= 
				COMPANY_NAME.'

				'.COMPANY_STREET.'
				'.COMPANY_ZIP.' '.COMPANY_PLACE.'
				E-Mail: '.EMAIL_IMPRINT.'
				Telefon: '.COMPANY_PHONE_STATIONARY.'
				
				Geschäftsführer: '.COMPANY_BOSS.'
				
				Allgemeine Geschäftsbedingungen:
				<a href="'.DOMAIN.'/conditions">'.DOMAIN.'/conditions</a>
				
				Hinweis zur Streitbeilegung gemäß § 36 VSBG:
				Wir nehmen nicht an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teil und sind hierzu auch nicht verpflichtet.
				
				Per §19 Abs. 1 UStG wird die Umsatzsteuer nicht ausgewiesen.
				
				<a class="btn btn-primary mb-3" href="'.DOMAIN.'">Zur Startseite</a>';
			
				$essentials -> getViewFactory() -> echoFeed(NULL, $imprintHTMLContent);
			?>
		</div>
		<?php
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
