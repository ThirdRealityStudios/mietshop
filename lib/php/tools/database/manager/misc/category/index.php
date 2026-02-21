<?php
	class CategoryManager
	{
		private DatabaseManager $databaseManager;

		public function __construct(DatabaseManager $databaseManager)
		{
			$this -> databaseManager = $databaseManager;
		}

		/**
		 * @return DatabaseManager
		 */
		private function getDatabaseManager(): DatabaseManager
		{
			return $this->databaseManager;
		}

		// Sets up the Category table if it was not created yet.
		// Necessary to make the CategoryManager work.
		public function initDatabase() : bool
		{
			$sqlSetupCheck = "

			  CREATE TABLE IF NOT EXISTS Category
			  (
				CategoryID INT(8) AUTO_INCREMENT PRIMARY KEY,
				Title VARCHAR(32) NOT NULL UNIQUE
			  );

			";

			return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
		}
		
	   /**
		* Processes the category id which should be used for a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['category'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['category'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processCategory($id, $checkSyntaxOnly)
		{
			if(isset($_POST['category']))
			{
				if(is_numeric($_POST['category']))
				{
					if($checkSyntaxOnly)
						return true;
					
					$updateSuccessful = $this -> getDatabaseManager()->update('Article', 'CategoryID', $_POST['category'], "ArticleID = $id");
					
					if($updateSuccessful)
					{					
						// The update in database was succesful.
						return true;
					}
					else
					{
						// The update in database failed.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_CATEGORY_FAILED."</p>";

						return false;
					}
				}
				else
				{
					// Category value provided for article does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_CATEGORY."</p>";

					return false;
				}
			}
			else
			{
				// Hence no category was provided for the string table to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}
	}
?>
