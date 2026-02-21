<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/limits/index.php';

	class StringTableManager
	{
		private DatabaseManager $databaseManager;

		private function getDatabaseManager()
		{
			return $this->databaseManager;
		}

		public function __construct($databaseManager)
		{
			$this -> databaseManager = $databaseManager;
		}

		// Sets up the StringTable table if it was not created yet.
		// Necessary to make the StringTableManager work.
		public function initDatabase(): bool
        {
			$sqlSetupCheck = "

			CREATE TABLE IF NOT EXISTS StringTable
			(
				StringTableID INT(8) AUTO_INCREMENT PRIMARY KEY,
				UploadHash VARCHAR(32),

				Title VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),

				Identifier0 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),
				Description0 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),

				Identifier1 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),
				Description1 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),

				Identifier2 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),
				Description2 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),

				Identifier3 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),
				Description3 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),

				Identifier4 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH."),
				Description4 VARCHAR(".LIMIT_POST_STRING_TABLE_FIELD_LENGTH.")
			);

			";

			return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
		}
		
		/**
		* Processes the title of the string table of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $stringTableID The needed ID to find the record in table StringTable for updating the post / article (string table record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['stringTableTitleXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['stringTableTitleXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processStringTableTitle($stringTableID, $checkSyntaxOnly)
		{
			if(isset($_POST['stringTableTitle']))
			{
				// Provided value must be a string (and not too long for website).
				// It is also allowed to be empty.
				if(is_string($_POST['stringTableTitle']) && strlen($_POST['stringTableTitle']) <= LIMIT_POST_STRING_TABLE_FIELD_LENGTH)
				{
					if($checkSyntaxOnly)
						return true;
					
					$updateSuccessful = $this -> getDatabaseManager() -> update('StringTable', 'Title', $_POST['stringTableTitle'], "StringTableID = $stringTableID");
					
					if($updateSuccessful)
					{					
						// The update in database was succesful.
						return true;
					}
					else
					{
						// The update in database failed.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_STRING_TABLE_TITLE_FAILED."</p>";

						return false;
					}
				}
				else
				{
					// Title provided for string table does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_TITLE."</p>";

					return false;
				}
			}
			else
			{
				// Hence no title was provided for the string table to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}
		
		/**
		* Processes the HTML element stringTableIdentifierXYZ of the string table of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $stringTableID The needed ID to find the record in table StringTable for updating the post / article (string table record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param integer $index The needed index to know which field is meant to update, e.g. stringTableIdentifier0, stringTableIdentifier1, stringTableIdentifier2, ... (valid values usually exist from 0 - 4)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['stringTableIdentifierXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['stringTableIdentifierXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processStringTableIdentifier($stringTableID, $index, $checkSyntaxOnly)
		{
			if(is_numeric($index) && $index >= 0)
			{
				if(isset($_POST['stringTableIdentifier'.$index]))
				{
					// Provided value must be a string (and not too long for website).
					// It is also allowed to be empty.
					if(is_string($_POST['stringTableIdentifier'.$index]) && strlen($_POST['stringTableIdentifier'.$index]) <= LIMIT_POST_STRING_TABLE_FIELD_LENGTH)
					{
						if($checkSyntaxOnly)
							return true;
						
						$updateSuccessful = $this -> getDatabaseManager() -> update('StringTable', 'Identifier'.$index, $_POST['stringTableIdentifier'.$index], "StringTableID = $stringTableID");

						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_STRING_TABLE_IDENTIFIER_XYZ_FAILED." (Element: ".$index.")</p>";

							return false;
						}
					}
					else
					{
						// Title provided for string table does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_IDENTIFIER_XYZ." (Element: ".$index.")</p>";

						return false;
					}
				}
				else
				{
					// Hence no value was provided for stringTableIdentifierXYZ in the string table to change,
					// return true,
					// as it has no effect (old value remains).
					return true;
				}
			}
			else
			{
				// The provided index is invalid / bullshit.
				return false;
			}
		}
	
		/**
		* Updates the HTML element stringTableDescriptionXYZ of the string table of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $stringTableID The needed ID to find the record in table StringTable for updating the post / article (string table record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param integer $index The needed index to know which field is meant to update, e.g. stringTableDescription0, stringTableDescription1, stringTableDescription2, ... (valid values usually exist from 0 - 4)
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['stringTableDescriptionXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['stringTableDescriptionXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processStringTableDescription($stringTableID, $index, $checkSyntaxOnly)
		{
			if(is_numeric($index) && $index >= 0)
			{
				if(isset($_POST['stringTableDescription'.$index]))
				{
					// Provided value must be a string (and not too long for website).
					// It is also allowed to be empty.
					if(is_string($_POST['stringTableDescription'.$index]) && strlen($_POST['stringTableDescription'.$index]) <= LIMIT_POST_STRING_TABLE_FIELD_LENGTH)
					{
						if($checkSyntaxOnly)
							return true;
						
						$updateSuccessful = $this -> getDatabaseManager() -> update('StringTable', 'Description'.$index, $_POST['stringTableDescription'.$index], "StringTableID = $stringTableID");
						
						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_STRING_TABLE_DESCRIPTION_XYZ_FAILED." (Element: ".$index.")</p>";

							return false;
						}
					}
					else
					{
						// Title provided for string table does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_DESCRIPTION_XYZ." (Element: ".$index.")</p>";

						return false;
					}
				}
				else
				{
					// Hence no value was provided for stringTableDescriptionXYZ in the string table to change,
					// return true,
					// as it has no effect (old value remains).
					return true;
				}
			}
			else
			{
				// The provided index is invalid / bullshit.
				return false;
			}
		}
		
		/**
		* Removes completely empty rows between filled rows (also partially filled rows) of a given string table
		* (usually after uploading or editing an article).
		* If something goes wrong (e.g. update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $stringTableID The id of string table of an article.
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully removed empty rows. Otherwise returns 'false'.
		*/
		private function removeEmptyRows($stringTableID)
		{
			$potentialErrorMessage = "<p>".LANG_RENTAL_POST_UPDATE_INTERNAL_ERROR_STRING_TABLE_SORTING."</p>";
			
			$identifier0 = $this -> getDatabaseManager() -> selectFirst('Identifier0', 'StringTable', "StringTableID = $stringTableID");
			if($identifier0 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$description0 = $this -> getDatabaseManager() -> selectFirst('Description0', 'StringTable', "StringTableID = $stringTableID");
			if($description0 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$identifier1 = $this -> getDatabaseManager() -> selectFirst('Identifier1', 'StringTable', "StringTableID = $stringTableID");
			if($identifier1 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$description1 = $this -> getDatabaseManager() -> selectFirst('Description1', 'StringTable', "StringTableID = $stringTableID");
			if($description1 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$identifier2 = $this -> getDatabaseManager() -> selectFirst('Identifier2', 'StringTable', "StringTableID = $stringTableID");
			if($identifier2 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$description2 = $this -> getDatabaseManager() -> selectFirst('Description2', 'StringTable', "StringTableID = $stringTableID");
			if($description2 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$identifier3 = $this -> getDatabaseManager() -> selectFirst('Identifier3', 'StringTable', "StringTableID = $stringTableID");
			if($identifier3 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$description3 = $this -> getDatabaseManager() -> selectFirst('Description3', 'StringTable', "StringTableID = $stringTableID");
			if($description3 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$identifier4 = $this -> getDatabaseManager() -> selectFirst('Identifier4', 'StringTable', "StringTableID = $stringTableID");
			if($identifier4 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$description4 = $this -> getDatabaseManager() -> selectFirst('Description4', 'StringTable', "StringTableID = $stringTableID");
			if($description4 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$identifierUnsorted = array($identifier0, $identifier1, $identifier2, $identifier3, $identifier4);
			$descriptionUnsorted = array($description0, $description1, $description2, $description3, $description4);
			
			$identifierSorted = array('', '', '', '', '');
			$descriptionSorted = array('', '', '', '', '');
			
			$sortedIndex = 0;
			
			for($i = 0; $i < 5; $i++)
			{
				if(empty($identifierUnsorted[$i]) && empty($descriptionUnsorted[$i]))
					continue;
				
				$identifierSorted[$sortedIndex] = $identifierUnsorted[$i];
				$descriptionSorted[$sortedIndex] = $descriptionUnsorted[$i];

				$sortedIndex++;
			}
			
			$updateSuccessful = true;
			
			// Upload the sorted string table now to the SQL DB.
			// After this, there are no completely empty rows in between (partially) filled ones.
			for($i = 0; $i < 5; $i++)
			{
				$updateSuccessful &= $this -> getDatabaseManager() -> update('StringTable', 'Identifier'.$i, $identifierSorted[$i], "StringTableID = $stringTableID");
				$updateSuccessful &= $this -> getDatabaseManager() -> update('StringTable', 'Description'.$i, $descriptionSorted[$i], "StringTableID = $stringTableID");
			}
			
			if($updateSuccessful)
			{
				// The update in database was succesful.
				return true;
			}
			else
			{
				// The update in database failed.
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
			}
			
			return false;
		}

		/**
		 * Creates an empty record in table StringTable of a post.
		 * If something goes wrong (e.g.creation of record in database or fetching of record ID failed),
		 * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		 *
		 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		 * @return integer ID of the created record or NULL if creation failed (detailed error message saved then in $_SESSION['UPDATE_ERROR_MSG'])
		 */
		public function createStringTable()
		{
			$uploadHash = md5(random_bytes(RANDOM_BYTES_VALUE)); // For recognition of record after creation (random hash generation).
			$creationSuccess = $this -> getDatabaseManager() -> insert('StringTable', $uploadHash);

			if(is_bool($creationSuccess) && $creationSuccess)
			{
				$stringTableID = $this -> getDatabaseManager() -> selectFirst('StringTableID', 'StringTable', "UploadHash LIKE \"%$uploadHash%\"");

				if(is_numeric($stringTableID) && $stringTableID >= 0)
				{
					return $stringTableID;
				}
				else
				{
					$_SESSION['UPDATE_ERROR_MSG'] = 'StringTable ID abfragen nach Erstellung von StringTable Datensatz fehlgeschlagen (DB Fehler?)';
				}
			}
			else
			{
				$_SESSION['UPDATE_ERROR_MSG'] = 'StringTable Datensatz erstellen fehlgeschlagen (DB Fehler)';
			}

			return NULL;
		}
		
		/**
		* Processes the contents of the string table of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['...'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['...'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processStringTable($id, $checkSyntaxOnly)
		{
			$stringTableID = NULL;
			
			if(!$checkSyntaxOnly)
			{
				// Fetch ID of the string table from the article,
				// so it can be found and updated in its own database table,
				// called StringTable.
				$stringTableID = $this -> getDatabaseManager() -> selectFirst('StringTableID', 'Article', "ArticleID = $id");
			}
			
			// Do not change order of minterms in if-statement!
			// Otherwise you change order of execution which is important.
			if(($stringTableID !== NULL || $checkSyntaxOnly) &&
				$this -> processStringTableTitle($stringTableID, $checkSyntaxOnly) &&
				$this -> processStringTableIdentifier($stringTableID, 0, $checkSyntaxOnly) &&
				$this -> processStringTableDescription($stringTableID, 0, $checkSyntaxOnly) &&
				$this -> processStringTableIdentifier($stringTableID, 1, $checkSyntaxOnly) &&
				$this -> processStringTableDescription($stringTableID, 1, $checkSyntaxOnly) &&
				$this -> processStringTableIdentifier($stringTableID, 2, $checkSyntaxOnly) &&
				$this -> processStringTableDescription($stringTableID, 2, $checkSyntaxOnly) &&
				$this -> processStringTableIdentifier($stringTableID, 3, $checkSyntaxOnly) &&
				$this -> processStringTableDescription($stringTableID, 3, $checkSyntaxOnly) &&
				$this -> processStringTableIdentifier($stringTableID, 4, $checkSyntaxOnly) &&
				$this -> processStringTableDescription($stringTableID, 4, $checkSyntaxOnly))
			{
				if($checkSyntaxOnly)
				{
					return true;
				}
				else
				{
					return $this -> removeEmptyRows($stringTableID); // post-processing string table data.
					// If the function also returns true, then everything could be updated successfully.
				}
			}
			else
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_POST_STRING_TABLE_ID_NOT_FOUND."</p>";
			}
			
			return false;
		}
	}
?>
