<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/popup/index.php';

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
	<body>
		<?php			
			$articleQuery = NULL;
			$article = NULL;
			
			// The article ID will change to a number > -1 if a valid article was found in the DB.
			$articleID = NULL;
			
			$isArticleIDCorrect = true;
			
			// Normally the case when you only want to see the edit page for normal editing.
			if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] >= 0)
			{
				$articleID = $_GET['id'];
			}
			// This is normally only the case when you already loaded the edit page
			// and clicked on the delete button for the current article
			// (in order to reload the edit page with a dialog that asks you with "Yes" or "No").
			else if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] >= 0)
			{
				$articleID = $_POST['id'];
			}
			else // Can't load any data for an article cuz no ID transmitted or valid.
			{
				$isArticleIDCorrect = false;
			}
			
			if($isArticleIDCorrect)
			{
				// Load article from DB with given article ID.
				$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', "ArticleID = $articleID", NULL);
			}
			
			if($articleQuery != NULL)
			{
				$article = $articleQuery[0];
			}
			
			// Normally $_POST['action'] was only sent when you want to delete the current article
			// (there are no other uses-cases currently implemented).
			// It is being sent if the user clicks on the delete button on an article edit page.
			// It will reload the page but with POST variables "id" and "action" sent.
			// Then this will trigger a popup here which asks you "You really want delete this article?".
			// Thats only why this is sent and processed in this if-statement below.
			if(isset($_POST['action']) && is_string($_POST['action']) && strcmp($_POST['action'], 'delete') == 0)
			{
				// echo genPopupHTML('Artikel unwiderruflich löschen?', 'POST', "<input type=\"hidden\" name=\"id\" value=\"$articleID\">", 'Ja', DOMAIN.'/edit/delete/', 'Nein');
			
				$preceedingHTML = '
				<p class="fs-1 lh-1 fw-bold mb-3">Bestätigen</p>
				<p class="fs-6 mb-3">Wollen Sie den Artikel wirklich unwiderruflich löschen?</p>';

				$firstAction = DOMAIN.'/edit/delete/';
				$firstHiddenInputName = 'id';
				$firstHiddenInputValue = $articleID;
				$firstButtonTitle = 'Ja';

				$secondAction = DOMAIN.'/edit/';
				$secondHiddenInputName = 'id';
				$secondHiddenInputValue = $articleID;
				$secondButtonTitle = 'Nein';

				echo genFullscreenPopup($preceedingHTML, $firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);
			}

			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
			
			$uploadFormHTML = '<div class="mb-3"><span class="fs-4">Artikel bearbeiten</span></div>';
			
			if($isArticleIDCorrect && $article != NULL)
			{
				$uploadFormHTML .= $essentials -> getDatabaseManager() -> getArticleManager() -> genHTMLForm(false, $article);
			}
			else
			{
				$uploadFormHTML .= '<span class="fs-6">Ooops.. Dieser Artikel scheint nicht zu existieren!</span>';
			}
		?>
		<div class="container-fluid">
		<?php
			$essentials -> getViewFactory() -> echoFeed(NULL, $uploadFormHTML);
		?>
		</div>
		<?php
			// Post-initialization and loading of necessary content after main part.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
