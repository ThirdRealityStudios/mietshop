<?php
	// This PHP file represents the HTML Bootstrap header
	// which will be shown on your website everywhere.
	// Do not get confused with the HTTP header (it is not!).
	// Also do not get confused with the HTML head tag.
	// It has nothing to do with it.
	// This file only represents the first inner part of your body tag.
	// This could be a navigation bar or whatever,
	// usually called 'header' in web terms.
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/popup/cookie/index.php';
?>
<nav class="navbar bg-primary-subtle sticky-top border-bottom border-3 border-primary w-100 z-2">
	<div class="container-fluid">
		<img src="<?php echo DOMAIN ?>/favicon.png" alt="favicon.png" width="70" height="70">
		<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" role="dialog">
			<div class="offcanvas-header bg-primary-subtle border-bottom border-3 border-primary">
				<h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?php echo COMPANY_NAME ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body bg-primary-subtle">
				<ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="<?php echo DOMAIN ?>">Startseite 🏠</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo DOMAIN.'/search' ?>">Jetzt <?php echo LANG_ARTICLE_OBJECT_NAME_PLURAL ?> entdecken 🔎</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo DOMAIN.'/login' ?>">Anmelden</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo DOMAIN.'/conditions' ?>">AGBs</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo DOMAIN.'/privacy' ?>">Datenschutzerklärung</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo DOMAIN.'/imprint' ?>">Impressum</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>