<?php
	class Update
	{
		/* Updates the given cells (by their hash identifiers).
		 * Will re-direct to the corresponding target URL after changes were made.
		 * Re-directs to the target page with the information whether it was successful (action=success) or not (action=failure).
		 *
		 * Missing features:
		 * - only update changed cells (currently does all = lots of traffic and performance-loss).
		 * - lock (critical) cells => to do so, maybe create a different SQL table which refers to all locked cells of other tables?
		 */

		private Essentials $essentials;

		private function getEssentials() : Essentials
		{
			return $this -> essentials;
		}

		public function __construct(Essentials $essentials)
		{
			$this -> essentials = $essentials;
		}

		// Frees up the space of the given table key.
		 private function destroy($tableKey) : void
		 {
			 $_SESSION[$tableKey] = null;
			 unset($_SESSION[$tableKey]);
		 }

		 // Signs the given session keys for heap deletion to free up space.
		 private function alertGC($verifiedKeys) : void
		 {
			 foreach($verifiedKeys as $elementKey)
			 {
				 $this -> destroy($elementKey);
			 }
		 }

		 // Returns an array of verified keys.
		 // The keys returned are yet known to the system / server session.
		 // If you send a form containing variables,
		 // this method will make sure only keys known to the session can be used ($_POST key = $_SESSION key).
		 // Helpful to avoid run-time errors (unknown SESSION keys).
		 private function getVerifiedKeys() : array
		 {
			 $sessionKeys = array_keys($_SESSION);
			 $postedKeys = array_keys($_POST);

			 $verifiedKeys = [];

			 foreach($postedKeys as $untestedKey)
			 {
				 // Check whether the transmitted session key is known to the server.
				 if(in_array($untestedKey, $sessionKeys))
				 {
					 // Verify the key if known to the system.
					 $verifiedKeys[] = $untestedKey;
				 }
			 }

			 return $verifiedKeys;
		 }

		 // Returns only the keys of the verified keys matching the given pattern.
		 private function getSessionKeysByPattern($verifiedKeys) : array
		 {
			 $patternKeys = [];

			 foreach($verifiedKeys as $verifiedKey)
			 {
				 if(str_contains($verifiedKey, 'insert'))
				 {
					 $patternKeys[] = $verifiedKey;
				 }
			 }

			 return $patternKeys;
		 }

		 // Returns only the keys of the verified keys NOT matching the given pattern.
		 private function getSessionKeysIgnorePattern($verifiedKeys, $pattern) : array
		 {
			 $insertKeys = [];

			 foreach($verifiedKeys as $verifiedKey)
			 {
				 if(!str_contains($verifiedKey, $pattern))
				 {
					 $insertKeys[] = $verifiedKey;
				 }
			 }

			 return $insertKeys;
		 }

		 private function getKeyValues($verifiedKeys) : array
		 {
			 $keyValues = [];

			 foreach($verifiedKeys as $verifiedKey)
			 {
				 $keyValues[] = $_POST[$verifiedKey];
			 }

			 return $keyValues;
		 }

		 // Returns the missing values as a SQL string for the prepared SQL insertion statement (see question mark).
		 private function getInsertionSQLValues($insertKeys)
		 {
			 	$insertValues = $this -> getKeyValues($insertKeys);

			 	$insertValuesString = "";

			 	for($index = 0; $index < sizeof($insertValues); $index++)
				{
					$rawValue = $insertValues[$index];

					$insertValue = (strlen($rawValue) == 0) ? "NULL" : "\"".$this -> getEssentials() -> getDatabaseManager() -> getSQLSafeValue($rawValue)."\"";

					if($index == (sizeof($insertValues) - 1))
					{
						$insertValuesString .= $insertValue;
					}
					else
					{
						$insertValuesString .= $insertValue.',';
					}
				}

				return $insertValuesString;
		 }

		 // Check all insert fields (so the whole insert form) whether they are empty.
		 private function isInsertFormEmpty($insertKeys)
		 {
			 foreach($insertKeys as $insertKey)
			 {
				 if($_POST[$insertKey] != null)
				 {
					 return false;
				 }
			 }

			 return true;
		 }

		 private function insert($insertKeys)
		 {
			 $criteriasFulfilled = true;

			 // Check for invalid fields before insertion.
			 foreach($insertKeys as $insertKey)
			 {
				 // Load insert criterias for the given column / key.
				 $insertCriterias = $_SESSION[$insertKey.'Criteria'];

				 $_SESSION[$insertKey.'Status'] = '';

				 // Check transmitted user inputs (POST variables).
				 if($_POST[$insertKey] == null && str_contains($insertCriterias, '%NOTNULL%'))
				 {
					 $_SESSION[$insertKey.'Status'] .= '%NOTNULL%';

					 $criteriasFulfilled = false;
				 }
			 }

			 $sqlExecutionSuccesful = true;

			 if($criteriasFulfilled)
			 {
				 $insertValuesString = $this -> getInsertionSQLValues($insertKeys);

				 $sqlExecutionSuccesful = $this -> getEssentials() -> getDatabaseManager() -> execParam($_SESSION['insert'], $insertValuesString);
			 }

			 // Mark the used insertion elements for heap deletion to free up space.
			 $this -> alertGC($insertKeys);

			 return $criteriasFulfilled && $sqlExecutionSuccesful;
		 }

		 // Updates the existing values of the yet selected table.
		 private function updateTable($tableKeys)
		 {
			 // The SQL execution needs to be 'true' by default as an empty table cause bugs otherwise (when no SQL is executed then).
			 $sqlExecutionSuccesful = true;

			 foreach($tableKeys as $tableKey)
			 {
				 $sqlExecutionSuccesful &= $this -> getEssentials() -> getDatabaseManager() -> execParam($_SESSION[$tableKey], $this -> getEssentials() -> getDatabaseManager() -> getSQLSafeValue($_POST[$tableKey]));
			 }

			 return $sqlExecutionSuccesful;
		 }

		function update($target)
		{
			$verifiedKeys = $this -> getVerifiedKeys();
			$insertKeys = $this -> getSessionKeysByPattern($verifiedKeys);

			$tableKeys = $this -> getSessionKeysIgnorePattern($verifiedKeys, 'insert');

			$insertionSuccesful = false;
			$tableUpdateSuccessful = false;

			$insertFieldsEmpty = $this -> isInsertFormEmpty($insertKeys);

			// Check if the user wants to create a new record for the table
			// (so he entered something into the corresponding input fields).
			if(!$insertFieldsEmpty)
			{
				$insertionSuccesful = $this -> insert($insertKeys);
			}

			// Also, update the table with the field values
			// (which are ones loaded from database and maybe changed).
			$tableUpdateSuccessful = $this -> updateTable($tableKeys);

			$success = ($insertionSuccesful || $insertFieldsEmpty) && $tableUpdateSuccessful;

			// Boolean which tells you if the link already contains a parameter or not (by checking the question mark ?).
			$isParameterized = str_contains($target, '?');

			// TODO: Make 'action' work as single param too (? symbol).
			if($success)
			{
				header("Location: $target".($isParameterized ? '&' : '?')."action=success");
			}
			else
			{
				header("Location: $target".($isParameterized ? '&' : '?')."action=failure");
			}
		}
	}
?>
