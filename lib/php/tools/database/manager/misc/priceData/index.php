<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/limits/index.php';

	class PriceDataManager
	{
		private DatabaseManager $databaseManager;

		/**
		 * @return DatabaseManager
		 */
		private function getDatabaseManager(): DatabaseManager
		{
			return $this -> databaseManager;
		}

		public function __construct($databaseManager)
		{
			$this -> databaseManager = $databaseManager;
		}

		// Sets up the PriceData table if it was not created yet.
		// Necessary to make the PriceDataManager work.
		public function initDatabase(): bool
        {
			$sqlSetupCheck = "

			CREATE TABLE IF NOT EXISTS PriceData
			(
				PriceDataID INT(8) AUTO_INCREMENT PRIMARY KEY,
				UploadHash VARCHAR(32),

				Price0 FLOAT(".LIMIT_POST_PRICE_DATA_FIELD_LENGTH."),
				Time0 INT(8),
				Unit0 INT(8),

				Price1 FLOAT(".LIMIT_POST_PRICE_DATA_FIELD_LENGTH."),
				Time1 INT(8),
				Unit1 INT(8),

				Price2 FLOAT(".LIMIT_POST_PRICE_DATA_FIELD_LENGTH."),
				Time2 INT(8),
				Unit2 INT(8),

				Price3 FLOAT(".LIMIT_POST_PRICE_DATA_FIELD_LENGTH."),
				Time3 INT(8),
				Unit3 INT(8),

				Price4 FLOAT(".LIMIT_POST_PRICE_DATA_FIELD_LENGTH."),
				Time4 INT(8),
				Unit4 INT(8)
			);

			";

			return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
		}

		/**
		* Removes completely empty rows between filled rows (also partially filled rows) of a given PriceData table
		* (usually after uploading or editing an article).
		* If something goes wrong (e.g. update in database failed),
		* it will save a detailed Bootstrap-formatted error message text in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $priceDataID The id of PriceData table of an article.
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully removed empty rows. Otherwise returns 'false'.
		*/
		private function removeEmptyRows($priceDataID)
		{
			$potentialErrorMessage = "<p>".LANG_RENTAL_POST_UPDATE_INTERNAL_ERROR_PRICE_DATA_SORTING."</p>";

			$price0 = $this -> getDatabaseManager() -> selectFirst('Price0', 'PriceData', "PriceDataID = $priceDataID");
			if($price0 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage.' (Price0)';
				
				return false;
			}
			
			$time0 = $this -> getDatabaseManager() -> selectFirst('Time0', 'PriceData', "PriceDataID = $priceDataID");
			if($time0 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage.' (Time0)';
				
				return false;
			}
			
			$unit0 = $this -> getDatabaseManager() -> selectFirst('Unit0', 'PriceData', "PriceDataID = $priceDataID");
			if($unit0 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage.' (TimeUnit0)';
				
				return false;
			}
			
			$price1 = $this -> getDatabaseManager() -> selectFirst('Price1', 'PriceData', "PriceDataID = $priceDataID");
			if($price1 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$time1 = $this -> getDatabaseManager() -> selectFirst('Time1', 'PriceData', "PriceDataID = $priceDataID");
			if($time1 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$unit1 = $this -> getDatabaseManager() -> selectFirst('Unit1', 'PriceData', "PriceDataID = $priceDataID");
			if($unit1 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$price2 = $this -> getDatabaseManager() -> selectFirst('Price2', 'PriceData', "PriceDataID = $priceDataID");
			if($price2 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$time2 = $this -> getDatabaseManager() -> selectFirst('Time2', 'PriceData', "PriceDataID = $priceDataID");
			if($time2 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$unit2 = $this -> getDatabaseManager() -> selectFirst('Unit2', 'PriceData', "PriceDataID = $priceDataID");
			if($unit2 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$price3 = $this -> getDatabaseManager() -> selectFirst('Price3', 'PriceData', "PriceDataID = $priceDataID");
			if($price3 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$time3 = $this -> getDatabaseManager() -> selectFirst('Time3', 'PriceData', "PriceDataID = $priceDataID");
			if($time3 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$unit3 = $this -> getDatabaseManager() -> selectFirst('Unit3', 'PriceData', "PriceDataID = $priceDataID");
			if($unit3 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$price4 = $this -> getDatabaseManager() -> selectFirst('Price4', 'PriceData', "PriceDataID = $priceDataID");
			if($price4 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$time4 = $this -> getDatabaseManager() -> selectFirst('Time4', 'PriceData', "PriceDataID = $priceDataID");
			if($time4 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$unit4 = $this -> getDatabaseManager() -> selectFirst('Unit4', 'PriceData', "PriceDataID = $priceDataID");
			if($unit4 === NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage;
				
				return false;
			}
			
			$priceUnsorted = array($price0, $price1, $price2, $price3, $price4);
			$timeUnsorted = array($time0, $time1, $time2, $time3, $time4);
			$unitUnsorted = array($unit0, $unit1, $unit2, $unit3, $unit4);
			
			$priceSorted = array('', '', '', '', '');
			$timeSorted = array('', '', '', '', '');
			$unitSorted = array('', '', '', '', '');
			
			$sortedIndex = 0;

			for($i = 0; $i < 5; $i++)
			{
				if(empty($priceUnsorted[$i]) && empty($timeUnsorted[$i]))
					continue;
				
				$priceSorted[$sortedIndex] = $priceUnsorted[$i];
				$timeSorted[$sortedIndex] = $timeUnsorted[$i];
				$unitSorted[$sortedIndex] = $unitUnsorted[$i];

				$sortedIndex++;
			}

			$updateSuccessful = true;
			
			// Upload the sorted PriceData table now to the SQL DB.
			// After this, there are no completely empty rows in between (partially) filled ones.
			for($i = 0; $i < 5; $i++)
			{
				$updateSuccessful &= $this -> getDatabaseManager() -> update('PriceData', 'Price'.$i, $priceSorted[$i], "PriceDataID = $priceDataID");
				$updateSuccessful &= $this -> getDatabaseManager() -> update('PriceData', 'Time'.$i, $timeSorted[$i], "PriceDataID = $priceDataID");
				$updateSuccessful &= $this -> getDatabaseManager() -> update('PriceData', 'Unit'.$i, $unitSorted[$i], "PriceDataID = $priceDataID");
			}

			if($updateSuccessful)
			{
				// The update in database was succesful.
				return true;
			}
			else
			{
				// The update in database failed.
				$_SESSION['UPDATE_ERROR_MSG'] = $potentialErrorMessage.' (for-loop at end failed)';
			}
			
			return false;
		}

		/**
		* Processes the HTML element PriceXYZ of the PriceData table of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $priceDataID The needed ID to find the record in table PriceData for updating the post / article (PriceData record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param integer $index The needed index to know which field is meant to update, e.g. Price0, Price1, Price2, ... (valid values usually exist from 0 - 4)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['PriceXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['PriceXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processPriceDataPrice($priceDataID, $index, $checkSyntaxOnly)
		{
			if(is_numeric($index) && $index >= 0)
			{
				if(isset($_POST['Price'.$index]))
				{
					// Provided value must be a (float) number and at least a specific value, defined by LIMIT_POST_PRICE_DATA_FIELD_PRICE_MIN (and symbol length must not be too long for website).
					// It is also allowed to be empty.
					if(empty($_POST['Price'.$index]) || (is_numeric($_POST['Price'.$index]) || is_float($_POST['Price'.$index])) && $_POST['Price'.$index] >= LIMIT_POST_PRICE_DATA_FIELD_PRICE_MIN && strlen($_POST['Price'.$index].'') <= LIMIT_POST_PRICE_DATA_FIELD_LENGTH)
					{
						if($checkSyntaxOnly)
							return true;
						
						$updateSuccessful = $this -> getDatabaseManager() -> update('PriceData', 'Price'.$index, $_POST['Price'.$index], "PriceDataID = $priceDataID");
						
						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_PRICE_DATA_PRICE_XYZ_FAILED." (Element: ".$index.")</p>";
						}
					}
					else
					{
						// Value provided for PriceData table does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_PRICE_XYZ." (Element: ".$index.")</p>";
					}
				}
				else
				{
					// Hence no value was provided for PriceXYZ in the PriceData table to change,
					// return true,
					// as it has no effect (old value remains).
					return true;
				}
			}
			else
			{
				// The provided index is invalid / bullshit.
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ELEMENT_PRICE_DATA_PRICE_ID."</p>";
			}
			
			return false;
		}

		/**
		* Process the data provided by HTML element timeXYZ,
		* used for defining a fixed minimum amount of time for renting an object (multiple options may exist and different time units),
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $priceDataID The needed ID to find the record in table PriceData for updating the post / article (PriceData record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param integer $index The needed index to know which field is meant to update, e.g. minTime0, minTime1, minTime2, ... (valid values usually exist from 0 - 4)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['minTimeXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['minTimeXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processPriceDataTime($priceDataID, $index, $checkSyntaxOnly)
		{
			if(is_numeric($index) && $index >= 0)
			{
				if(isset($_POST['Time'.$index]))
				{
					// Provided value must be an integer number and at least LIMIT_POST_PRICE_DATA_FIELD_TIME_MIN (and symbol length must not be too long for website).
					// It is also allowed to be empty.
					if(empty($_POST['Time'.$index]) || (is_numeric($_POST['Time'.$index]) && $_POST['Time'.$index] >= LIMIT_POST_PRICE_DATA_FIELD_TIME_MIN && $_POST['Time'.$index] <= HOURS_ONE_YEAR && strlen($_POST['Time'.$index].'') <= LIMIT_POST_PRICE_DATA_FIELD_LENGTH))
					{
						if($checkSyntaxOnly)
							return true;
						
						$updateSuccessful = $this -> getDatabaseManager() -> update('PriceData', 'Time'.$index, $_POST['Time'.$index], "PriceDataID = $priceDataID");
						
						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_TIME_XYZ." (Element: ".$index.")</p>";
						}
					}
					else
					{
						// Value provided for minTimeXYZ does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_TIME_XYZ." (Element: ".$index.")</p>";
					}
				}
				else
				{
					// Hence no value was provided for minTimeXYZ in order to change a rate option,
					// return true,
					// as it has no effect (old value remains).
					return true;
				}
			}
			else
			{
				// The provided index is invalid / bullshit.
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ELEMENT_PRICE_DATA_ID."</p>";
			}
			
			return false;
		}

		/**
		* Processes the used time unit by HTML element timeUnitXYZ (= radio buttons on edit page),
		* used for selecting a time unit (e.g. hours, days or none),
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $priceDataID The needed ID to find the record in table PriceData for updating the post / article (PriceData record ID needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param integer $index The needed index to know which field is meant to update, e.g. timeUnit0, timeUnit1, timeUnit2, ... (valid values usually exist from 0 - 4)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['timeUnitXYZ'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['timeUnitXYZ'] was sent but DB update failed or syntax was incorrect).
		*/
		private function processPriceDataUnit($priceDataID, $index, $checkSyntaxOnly)
		{
			if(is_numeric($index) && $index >= 0)
			{
				if(isset($_POST['Unit'.$index]))
				{
					// Provided option value of radio button for the selected time unit must be a number (radio buttons have all to be selected!).
					// Also, the transmitted value from radio button must be at least 0 and can only be as high as there are options for time units (including the option 'none' - if implemented so far).
					// Also, the transmitted value MUST not be empty.
					if(is_numeric($_POST['Unit'.$index]) && $_POST['Unit'.$index] >= 0 && $_POST['Unit'.$index] <= LIMIT_POST_PRICE_DATA_AMOUNT_TIME_UNITS)
					{
						if($checkSyntaxOnly)
							return true;
						
						$updateSuccessful = $this -> getDatabaseManager() -> update('PriceData', 'Unit'.$index, $_POST['Unit'.$index], "PriceDataID = $priceDataID");
						
						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_PRICE_DATA_UNIT_XYZ_FAILED." (Element: ".$index.")</p>";

							return false;
						}
					}
					else
					{
						// Value provided for timeUnitXYZ does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_UNIT_XYZ." (Element: ".$index.")</p>";

						return false;
					}
				}
				else
				{
					// Hence no value was provided for timeUnitXYZ in order to change a rate option,
					// return true,
					// as it has no effect (old value remains).
					return true;
				}
			}
			else
			{
				// The provided index is invalid / bullshit.
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ELEMENT_PRICE_DATA_ID."</p>";

				return false;
			}
		}

		/**
		 * Creates an empty record in table PriceData of a post.
		 * If something goes wrong (e.g.creation of record in database or fetching of record ID failed),
		 * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		 *
		 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		 * @return integer ID of the created record or NULL if creation failed (detailed error message saved then in $_SESSION['UPDATE_ERROR_MSG'])
		 */
		public function createPriceData()
		{
			$uploadHash = md5(random_bytes(RANDOM_BYTES_VALUE)); // For recognition of record after creation (random hash generation).
			$creationSuccess = $this -> getDatabaseManager() -> insert('PriceData', $uploadHash);

			if(is_bool($creationSuccess) && $creationSuccess)
			{
				$priceDataID = $this -> getDatabaseManager() -> selectFirst('PriceDataID', 'PriceData', "UploadHash LIKE \"%$uploadHash%\"");

				if(is_numeric($priceDataID) && $priceDataID >= 0)
				{
					return $priceDataID;
				}
				else
				{
					$_SESSION['UPDATE_ERROR_MSG'] = 'PriceData ID abfragen nach Erstellung von PriceData Datensatz fehlgeschlagen (DB Fehler?)';
				}
			}
			else
			{
				$_SESSION['UPDATE_ERROR_MSG'] = 'PriceData Datensatz erstellen fehlgeschlagen (DB Fehler)';
			}

			return NULL;
		}

		/**
		* Processes the contents of the PriceData table of a post,
		* used for price declarations of your rental object,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed (value $_POST['...'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['...'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processPriceData($id, $checkSyntaxOnly)
		{
			// Check if all price data has been sent:
			for($i = 0; $i < 5; $i++)
			{
				if(!isset($_POST['Price'.$i]))
				{
					return false;
				}

				if(!isset($_POST['Time'.$i]))
				{
					return false;
				}

				if(!isset($_POST['Unit'.$i]))
				{
					return false;
				}
			}

			$priceDataID = NULL;

			if(!$checkSyntaxOnly)
				$priceDataID = $this -> getDatabaseManager() -> selectFirst('PriceDataID', 'Article', "ArticleID = $id");

			$processedRowSuccessfully = true;

			for($i = 0; $i < 5; $i++)
			{
				$processedRowSuccessfully &= $this -> processPriceDataTime($priceDataID, $i, $checkSyntaxOnly);
				$processedRowSuccessfully &= $this -> processPriceDataPrice($priceDataID, $i, $checkSyntaxOnly);
				$processedRowSuccessfully &= $this -> processPriceDataUnit($priceDataID, $i, $checkSyntaxOnly);

				if(!$processedRowSuccessfully)
				{
					return false;
				}
			}

			if($priceDataID !== NULL || $checkSyntaxOnly)
			{
				if($checkSyntaxOnly)
				{
					return true;
				}
				else
				{
					return $this -> removeEmptyRows($priceDataID); // post-processing PriceData table data.
					// If the function also returns true, then everything could be updated successfully.
				}
			}
			else
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_POST_PRICE_DATA_ID_NOT_FOUND."</p>";

				return false;
			}
		}
	}
?>
