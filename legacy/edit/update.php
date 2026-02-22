<?php
	/**
	  *
	  * The "update.php" file is used to update entries of an yet existing article / post.
	  * With a little trick you can also use the functions of this .php file
	  * if you want to create or upload a new article / post.
	  *
	  */
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db/tools/shop/updateManager.php';

	$essentials = new Essentials();
	
	$_SESSION['UPDATE_ERROR_MSG'] = '';
	
	$id = -1;
	
  if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] >= 0)
  {
	$id = $_POST['id'];
	
	$idExists = $essentials -> getDatabaseManager() -> selectFirst('ArticleID', 'Article', "ArticleID = $id") != NULL;
	
	if($idExists)
	{
		// Now upload all changes to the article.
		// (if something goes wrong, an error message will be saved in session variable UPDATE_ERROR_MSG)
		$essentials -> getDatabaseManager() -> getArticleManager() -> upload($id);
	}
	else
	{
		$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_POST_ID_NOT_FOUND."</p>";
	}
  }
  else
  {
	  if(!isset($_POST['id']))
	  {
		  $_SESSION['UPDATE_ERROR_MSG'] = "<p>ID not set / trasnmitted</p>";
	  }
	  else
	  {
		  $_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ID."</p>";	
	  }
  }
  
  // Print the error message from the point where the error was detected.
  // Anyway, there is not always an error message printed.
  echo $_SESSION['UPDATE_ERROR_MSG'];
  
  // Will be shown only when there was an error updating the post.
  // If there was no error,
  // then JavaScript will redirect the user to the updated post.
  // Also helpful,
  // if JavaScript was turned off for whatever reason...
  echo '<p style="font-weight: bold;">', LANG_RENTAL_POST_UPDATE_FAILED, '</p>';
  echo '<a href="', DOMAIN, '/search">Zurück zur Suchseite</a>';
?>
