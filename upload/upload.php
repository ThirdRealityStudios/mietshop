<?php
	/**
	  *
	  * The "update.php" file is used to update entries of an yet existing article / post.
	  * With a little trick you can also use the functions of this .php file
	  * if you want to create or upload a new article / post.
	  *
	  */

	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

	$essentials = new Essentials();

	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db/tools/shop/updateManager.php';

	$_SESSION['UPDATE_ERROR_MSG'] = '';

	if($essentials -> getDatabaseManager() -> getCategoryManager() -> processCategory(NULL, true) &&
		$essentials -> getDatabaseManager() -> getArticleManager() -> processPreviewText(NULL, true) &&
		$essentials -> getDatabaseManager() -> getArticleManager() -> processDescription(NULL, true) &&
		$essentials -> getDatabaseManager() -> getArticleManager() -> processStockAmount(NULL, true) &&
		$essentials -> getDatabaseManager() -> getStringTableManager() -> processStringTable(NULL, true) &&
		$essentials -> getDatabaseManager() -> getPriceDataManager() -> processPriceData(NULL, true))
	{
		// Every transmitted parameter was at ths point verified to be valid (if-statement above).
		// Hence, it is safe to create an article / post with all transmitted values now (code below).
		
		$galleryID = $essentials -> getDatabaseManager() -> getGalleryManager() -> createGallery();
		
		// Check if ID valid.
		// If not, the error message generated and saved in $_SESSION['UPDATE_ERROR_MSG'] by createGallery() will be outputed later.
		if($galleryID !== NULL)
		{
			$stringTableID = $essentials -> getDatabaseManager() -> getStringTableManager() -> createStringTable();

			if($stringTableID !== NULL)
			{
				$priceDataID = $essentials -> getDatabaseManager() -> getPriceDataManager() -> createPriceData();
				
				$articleID = $essentials -> getDatabaseManager() -> getArticleManager() -> createArticle($galleryID, $stringTableID, $priceDataID);
				
				if($articleID !== NULL)
				{
					// Now update all entries of all previously created records.
					// And if successful, redirect the user to the uploaded / created article page.
					$essentials -> getDatabaseManager() -> getArticleManager() -> upload($articleID);
				}
			}
		}
	}
	
	// Print the error message from the point where the error was detected.
	// Anyway, there is not always an error message printed.
	echo $_SESSION['UPDATE_ERROR_MSG'];

	echo '<a href="', DOMAIN, '/search">Zurück zur Suchseite</a>';

?>

