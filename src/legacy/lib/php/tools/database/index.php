<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/system/account/index.php';
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/misc/priceData/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/misc/gallery/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/misc/stringTable/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/misc/article/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/misc/category/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/system/homeAction/index.php';
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/system/reflection/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/system/parser/index.php';
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/config/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/limits/index.php';
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/log/index.php';
	
	// Because database manager is used in almost every PHP file,
	// it is smart to contain the language file too
	// (so I don't have to include it always in every single case)
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/lang/DE/index.php';
	
	class DatabaseManager
	{
		private PriceDataManager $priceDataManager;
		private GalleryManager $galleryManager;
		private StringTableManager $stringTableManager;

		private AccountManager $accountManager;

		private ArticleManager $articleManager;
		private CategoryManager $categoryManager;
		
		private HomeActionManager $homeActionManager;

		private ReflectionManager $reflectionManager;
		private Parser $parser;

		function __construct()
		{
			$this -> priceDataManager = new PriceDataManager($this);
			$this -> galleryManager = new GalleryManager($this);
			$this -> stringTableManager = new StringTableManager($this);

			$this -> accountManager = new AccountManager($this);

			$this -> articleManager = new ArticleManager($this);
			$this -> categoryManager = new CategoryManager($this);

			$this -> homeActionManager = new HomeActionManager($this);

			$this -> reflectionManager = new ReflectionManager($this);
			$this -> parser = new Parser($this);

			$success = $this -> initDatabase();

			if(!$success)
			{
				die('Could not initialize database. Stopped execution.');
			}
		}

		public function getStringTableManager()
		{
			return $this -> stringTableManager;
		}

		public function getPriceDataManager()
		{
			return $this -> priceDataManager;
		}

		public function getGalleryManager()
		{
			return $this -> galleryManager;
		}

		public function getAccountManager()
		{
			return $this -> accountManager;
		}

		public function getCategoryManager()
		{
			return $this -> categoryManager;
		}

		public function getArticleManager()
		{
			return $this -> articleManager;
		}

		public function getReflectionManager()
		{
			return $this -> reflectionManager;
		}

		public function getHomeActionManager()
		{
			return $this -> homeActionManager;
		}

		// Initializes DB tables etc., if not created for whatever reason and so on.
		private function initDatabase() : bool
		{
			$success = $this -> getAccountManager() -> initDatabase();
			$success &= $this -> getHomeActionManager() -> initDatabase();
			$success &= $this -> getPriceDataManager() -> initDatabase();
			$success &= $this -> getStringTableManager() -> initDatabase();
			$success &= $this -> getGalleryManager() -> initDatabase();
			$success &= $this -> getArticleManager() -> initDatabase();
			$success &= $this -> getCategoryManager() -> initDatabase();
			$success &= $this -> getReflectionManager() -> initDatabase();
			$success &= $this -> getParser() -> initDatabase();

			return $success;
		}


		/**
		 * Executes a SQL statement by preparing the statement and creating a PDO object for each call to this function.
		 *
		 * Attention!
		 *
		 * Not suitable for queries, such as "SELECT", because there is no usable array returned!
		 * For such cases use "select($column, $from, $where, $additionalSQL)" instead.
		 *
		 * @param string $sql SQL statement.
		 * @return bool true if SQL execution on server was successful, otherwise false.
		 * @throws PDOException
		 */
		private function perform(string $sql): bool
		{
			$pdo = $this -> connect();

			$prepared = $pdo -> prepare($sql);

			if($prepared !== false)
			{
				$success = $prepared -> execute();

				// Close connection and give other statements the chance to be executed.
				$success &= $prepared -> closeCursor();

				return $success;
			}
			else
			{
				return false;
			}
		}

		/**
		 * SELECTs or SHOWs all elements of either a column or property by the where-condition given.
		 * Uses PDO::FETCH_ASSOC.
		 *
		 * @param string $operation SQL operation. Can be either SELECT or SHOW.
		 * @param string $target Either SQL column name or property name, e.g. INDEXES (for SHOW).
		 * @param string $from SQL table.
		 * @param string|null $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
		 *
		 * @return array|null Either the query results or NULL if there was an DB execution error or an obvious SQL syntax error in your passed parameters.
		 * @throws PDOException
		 */
		private function query(string $operation, string $target, string $from, string|null $where): ?array
		{
			if((strcmp(strtolower($operation), "select") === 0 || strcmp(strtolower($operation), "show") === 0) && !str_contains($target, " ") && !str_contains($from, " "))
			{
				$queryString = "$operation $target FROM $from".($where === null ? "" : (" WHERE ".$where)).";";

				$pdo = $this -> connect();

				$prepared = $pdo -> prepare($queryString);

				if($prepared !== false && $prepared -> execute())
				{
					$fetchedArray = $prepared -> fetchAll(PDO::FETCH_ASSOC);

					// If the connection could not be closed successfully
					// (although data might be fetched),
					// it is preferred not to return the fetched data in order
					// to let the dev or admin fix the actual closure problem.
					// He will know because NULL is returned only at such moments.
					if($prepared -> closeCursor())
					{
						return $fetchedArray;
					}
					else
					{
						return null;
					}
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}

		/**
		 * Selects all elements in a given column in a table by the where-condition given.
		 * This function is not safe against SQL injection or SQL syntax errors,
		 * as it is meant for internal use only.
		 *
		 * @param string $column SQL table column.
		 * @param $from string SQL table.
		 * @param string|null $where SQL condition (not including WHERE, only condition - e.g. " Username='PatrickStar' AND Password='123' "). Use NULL to ignore.
		 *
		 * @return array|null Either the query results or NULL if the column or table was not found.
		 */
		public function select(string $column, string $from, string|null $where): ?array
		{
			return $this -> query("SELECT", $column, $from, $where);
		}

		/**
		 * Returns all column names of a table from DB
		 * (without the first ID column, as it is known usually).
		 *
		 * @param string $tableName SQL table name.
		 * @return string[]|null Either the column names or NULL (e.g. no results or SQL execution failed).
		 */
		private function getColumnNames(string $tableName): ?array
		{
			if(!empty($tableName) && !str_contains($tableName, " "))
			{
				$fetchedArray = $this -> select("DISTINCT COLUMN_NAME", "INFORMATION_SCHEMA.COLUMNS", "TABLE_NAME = '$tableName'");

				// "> 1" because there is also an ID column, among the other columns.
				if(is_array($fetchedArray) && sizeof($fetchedArray) > 1)
				{
					$rows = array_values($fetchedArray);

					if(sizeof($rows) > 0)
					{
						$columnNames = [];

						foreach($rows as $row)
						{
							if(is_array($row) && sizeof($row) > 0) // maybe is_array(..) will cause a bug if only one value is contained in $row = one string
							{
								$columnName = array_values($row)[0];

								$columnNames[] = $columnName;
							}
							else
							{
								return null;
							}
						}

						// Cut off the first column "ID" if it exists and directly return.
						if(sizeof($columnNames) > 1 && strcmp(strtolower($columnNames[0]), "id") === 0)
						{
							return array_slice($columnNames, 1, sizeof($columnNames) - 1);
						}
						else
						{
							return null;
						}
					}
					else
					{
						return null;
					}
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}

		/**
		 * Creates a valid SQL insertion command,
		 * using the provided table name (SQL FROM equivalent) and the values (string array) to insert.
		 *
		 * E.g. passing $tableName = "Account", $insertionValues = ["username123", "myPassword"] would result in:
		 *
		 * INSERT INTO Account(Username, Password) VALUES ('username123', 'myPassword') ON DUPLICATE KEY UPDATE Username=Username, Password=Password;
		 *
		 * @param string $tableName
		 * @param string ...$insertionValues
		 * @return string|null Returns only NULL if no columns could be found for the given table as string parameter.
		 */
		private function genInsertSQL(string $tableName, string ...$insertionValues): ?string
		{
			$insertSQL = 'INSERT INTO '.$tableName.'(';

			$columnNames = $this -> getColumnNames($tableName);

			if(is_array($columnNames) && sizeof($columnNames) > 0)
			{
				for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
				{
					$columnName = $columnNames[$i];

					if(is_string($columnName) && !empty($columnName))
					{
						// Add criterias to the current columns SQL (removed later before transmission).
						// $_SESSION['insert'.$columnName.'Criteria'] = $this -> getInsertionCriterias($tableName, $columnName);

						if($i < (sizeof($insertionValues) - 1))
						{
							$insertSQL .= "$columnName, ";
						}
						else
						{
							$insertSQL .= "$columnName) VALUES ('";
						}
					}
					else
					{
						return null;
					}
				}

				for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
				{
					$insertionValue = $insertionValues[$i];

					if(is_string($insertionValue) && !empty($insertionValue))
					{
						if($i < (sizeof($columnNames) - 2) && $i < (sizeof($insertionValues) - 1))
						{
							$insertSQL .= ($insertionValue."', '");
						}
						else
						{
							$insertSQL .= ($insertionValue."') ON DUPLICATE KEY UPDATE ");
						}
					}
					else
					{
						return null;
					}
				}

				for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
				{
					$columnName = $columnNames[$i];

					if(is_string($columnName) && !empty($columnName))
					{
						$insertSQL .= "$columnName = $columnName";

						if($i < (sizeof($insertionValues) - 1))
						{
							$insertSQL .= ", ";
						}
						else
						{
							$insertSQL .= ";";
						}
					}
					else
					{
						return null;
					}
				}

				return $insertSQL;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Creates a new dataset at the given destination with the given value(s).
		 *
		 * @param string $destination Where to insert the values at.
		 * @param string|string[] $value Comma-separated values, e.g. the string "123, '', 'Hello World!', -123.2323" or also just "Hello".
		 * @return bool True if the dataset could be created successfully. Otherwise, false (e.g. syntax error or DB error).
		 * @throws PDOException
		 */
		public function insert(mixed $destination, mixed $value): bool
		{
			if(is_string($destination) && strlen($destination) > 0 && !str_contains($destination, " ") && is_string($value))
			{
				return $this -> perform("INSERT INTO $destination VALUES($value)");
			}
			else
			{
				return false;
			}
		}

		/**
		 * Returns all tables (names) of the given database.
		 *
		 * @return string[]|NULL Either the table names or NULL if nothing found.
		 */
		public function getTableNames(): ?array
		{
			$sql = 'SHOW FULL TABLES';

			try
			{
				$pdo = $this -> connect();

				if(!is_null($pdo))
				{
					$prepared = $pdo -> prepare($sql);

					if($prepared !== false)
					{
						$isExecutionSuccessful = $prepared -> execute();

						if($isExecutionSuccessful)
						{
							$fetchedArray = $prepared -> fetchAll(PDO::FETCH_ASSOC);

							if(sizeof($fetchedArray) > 0)
							{
								$tableNames = array_values($fetchedArray);

								$isClosureSuccessful = $prepared -> closeCursor();

								if($isClosureSuccessful)
								{
									// $tableNamesOnly = array_fill(0, sizeof($tableNames), null);

									$tableNamesOnly = [];

									for($index = 0; $index < sizeof($tableNames); $index++)
									{
										$tableName = array_values($tableNames[$index])[0];

										if(is_string($tableName))
										{
											$tableNamesOnly[] = $tableName;
										}

										// $tableNamesOnly[] = array_values($tableNames[$index])[0];
									}

									return $tableNamesOnly;
								}
								else
								{
									return NULL;
								}
							}
							else
							{
								return NULL;
							}
						}
						else
						{
							return NULL;
						}
					}
					else
					{
						return NULL;
					}
				}
				else
				{
					return NULL;
				}
			}
			catch(PDOException $e)
			{
				return NULL;
			}
		}

		/**
		 * Returns a HTML combo box (<select><option> list, usually to use in a form) which contains all the entries of a DB table column.
		 *
 		 * @param string $tableName SQL table name (required).
		 * @param string $columnName SQL column name (required).
		 * @param string $comboBoxName The name applied for the HTML tag ID and name (required).
		 * @param string $additionalInnerHTML Additional inner HTML, e.g. style-property.
		 * @param int|NULL $defaultID The ID of the database entry you want to be the default option.
 		 * @return string|NULL Returns HTML code to display a combo box with values specified from the given SQL column. Otherwise returns NULL if the data could not be fetched from the column or if there is no data in the DB column.
 		 */
		public function getHTMLComboBox($tableName, $columnName, $comboBoxName, $additionalInnerHTML, $defaultID) : ?string
		{
			$rows = $this -> select(" DISTINCT $tableName".'ID'.", $columnName", $tableName, NULL, NULL);

			if(is_array($rows) && sizeof($rows) > 0)
			{
				$html = '<select class="form-select" id="'.$comboBoxName.'" name="'.$comboBoxName.'"'.(($additionalInnerHTML != NULL) ? $additionalInnerHTML : '').'>';

				foreach($rows as $row)
				{
					$optionValue = $row[$tableName.'ID'];
					$optionTitle = $row[$columnName];

					$html .= '<option value="'.$optionValue.'"'.($defaultID != NULL && $defaultID != -1 && $defaultID == $optionValue ? ' selected' : '').'>'.$optionTitle.'</option>';
				}

				$html .= '</select>';

				return $html;
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Replaces all special characters to different values to ensure safe SQL execution for values transmitted.
		 */
		public function getSQLSafeValue($value)
		{
			return str_replace("\"", "\"\"", $value);
		}

		/**
		 * Executes a SQL statement with one parameter identified as '?'.
		 *
		 * Attention!
		 *
		 * Not suitable for queries, such as "SELECT", because there is no usable array returned!
		 * For such cases use "select($column, $from, $where, $additionalSQL)" instead.
		 *
		 * @param $sql string SQL statement with one parameter ? to replace with $param.
		 * @param $param mixed Replacement value. Can also be NULL (PHP value) which will be interpreted as NULL in SQL.
		 * @return bool True if SQL execution on server was successful, otherwise false.
		 */
		public function execParam(string $sql, mixed $param): bool
		{
			$sqlWithValue = '';

			$sqlNull = $param == NULL || strlen($param) == 0;

			// Make sure all PHP NULL values are converted to SQL (otherwise error).
			if($sqlNull)
			{
				$param = 'NULL';

				$sqlWithValue = str_replace("\"?\"", $param, $sql);
			}
			else // No null value; continue as always (just repalce $param).
			{
				$sqlWithValue = str_replace("?", $param, $sql);
			}

			return $this -> perform($sqlWithValue);
		}

		/**
		 * Drops a given table from the database
		 * and it will not fail if the table does not exist (ignored then, return value is true anyway).
		 *
		 * The only way this function will return false is
		 * there was an error on the server due to
		 * SQL execution or bad syntax.
		 *
		 * @param string $tableName Table to drop.
		 * @return bool True if SQL execution on server was successful, otherwise false.
		 */
		public function drop(string $tableName): bool
		{
			$sql = "DROP TABLE IF EXISTS $tableName;";

			return $this -> perform($sql);
		}

		/**
		 * Drops all tables from the current database (which were created before by the user).
		 * System-relevant tables stay untouched by this operation, e.g. Account.
		 *
		 * @return void
		 */
		public function dropAll(): void
		{
			// Contains all table names from tables which are still alive in the database and created by the user.
			$tableNames = $this -> getParser() -> getAliveTableNames();

			if($tableNames != NULL)
			{
				foreach($tableNames as $tableName)
				{
					// Check for tables which are system-relevant and may not be deleted.
					if($tableName != 'Reflection' && $tableName != 'Account' && $tableName != 'HomeAction')
					{
						$this -> drop($tableName);
					}
				}
			}

			// Can be set to true if you have re-designed your system tables..
			$dropSystemTables = true;

			if($dropSystemTables)
			{
				// $this -> drop('Account');

				$this -> drop('PriceData');
				$this -> drop('StringTable');
				$this -> drop('Gallery');

				$this -> drop('Article');

				// $this -> drop('Category');
			}

			$this -> drop('Article');

			$this -> drop('SQLCreate');
		}

		/**
		 * Shows the attributes of a table by the where-condition given.
		 *
		 * @param string $property SQL property, e.g. TABLES or INDEXES.
		 * @param $from string SQL table.
		 * @param string|NULL $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
		 * @param string|NULL $additionalSQL Additional SQL which follows after the WHERE or FROM statement. Use NULL to ignore.
		 *
		 * @return array|NULL Either the query results or NULL if the column or table was not found.
		 */
		public function show(string $property, string $from, string|NULL $where, string|NULL $additionalSQL): ?array
		{
			return $this -> query("SHOW", $property, $from, $where, $additionalSQL);
		}

		/**
		 * Deletes one or multiple existing record(s) from a table.
		 *
		 * @param string $from SQL table.
		 * @param string|NULL $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
		 *
		 * @return bool True if SQL execution on server was successful, otherwise false.
		 */
		public function delete(string $from, string|NULL $where): bool
		{
			$sql = "DELETE FROM $from".(($where != NULL) ? " WHERE $where;" : ";");

			return $this -> perform($sql);
		}

		/**
		 * Used internally to return the first element of a query result.
		 *
		 * Helpful if there is only one element queried or if the first element is relevant only.
		 * Use cases: specific property or value is wanted (e.g. password hash of exactly one user or SQL system flags).
		 *
		 * @param array|NULL $selection SQL query results.
		 *
		 * @return mixed Either the first element or NULL if the passed query result was empty.
		 */
		private function getFirstElement(?array $selection): mixed
		{
			if(!is_null($selection) && sizeof($selection) > 0)
			{
				$firstColumn = array_values($selection)[0];

				return array_values($firstColumn)[0];
			}

			return NULL;
		}

		/**
		 * PROOFED
		 *
		 * Selects the first element (upper-left value of the table, imagine Excel or coordinate A0) of a selection, depending on the where-condition given.
		 * Returns NULL if the column or table was not found.
		 *
		 * @param string $column SQL table column.
		 * @param string $from SQL table.
		 * @param string|NULL $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
		 *
		 * @return mixed Either the first element or NULL if the query result was empty.
		 */
		public function selectFirst(string $column, string $from, ?string $where): mixed
		{
			$fetchedArray = $this -> select($column, $from, $where, NULL);

			if(is_array($fetchedArray) && sizeof($fetchedArray) > 0)
			{
				return $this -> getFirstElement($fetchedArray);
			}
			else
			{
				return NULL;
			}
		}

		public function getParser()
		{
			return $this -> parser;
		}

		// Drops all old tables and creates all tables specified in the Reflection table.
		// Additionally, creates system-relevant tables, such as "PriceData".
		public function create()
		{
			$this -> getParser() -> parse();

			$this -> initDatabase();
		}

		// Drops all tables which have been originally defined in the Reflection table.
		// Some non-user-defined tables, such as PriceData, will also be re-created.
		public function hardreset()
		{
			$this -> dropAll();
			$this -> create();
		}

		/**
		 * Returns the column names of a table (but without the ID column).
		 *
		 * Attention!
		 *
		 * This method only works if the first column of a table represents the (unique) IDs.
		 *
		 * @param string $tableName SQL table name.
		 * @return string[]|NULL Either the column names or NULL if the SQL execution failed.
		 */
		public function getColumnNamesWithoutID(string $tableName): ?array
		{
			$columnNames = $this -> getColumnNames($tableName);

			if(is_array($columnNames) && sizeof($columnNames) > 0)
			{
				return array_slice($columnNames, 1, sizeof($columnNames), false);
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Returns whether the given SQL column is nullable or not (= "NOT NULL" in SQL).
		 *
		 * @param string $tableName SQL table name.
		 * @param string $columnName SQL column name.
		 * @return bool Either true if the column is nullable or false.
		 */
		public function isNullable(string $tableName, string $columnName): bool
		{
			$fetchedValue = $this -> selectFirst("IS_NULLABLE", "INFORMATION_SCHEMA.COLUMNS", "TABLE_NAME = '$tableName' AND COLUMN_NAME = '$columnName'");

			if(!is_null($fetchedValue))
			{
				return strcmp($fetchedValue, "YES") == 0;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Prints an HTML table from a given query result.
		 *
		 * @param array|NULL $selection Query results.
		 * @param string $tableName SQL table name (must match the query table).
		 * @param string|NULL $htmlID HTML ID (for later referencing, optional). Use NULL to ignore.
		 */
		public function echoTable(?array $selection, string $tableName, ?string $htmlID): ?string
		{
			if(is_array($selection) && sizeof($selection) > 0)
			{
				if(is_string($tableName))
				{
					$columnNames = $this -> getColumnNames($tableName);

					if(is_array($columnNames) && sizeof($columnNames) > 0)
					{
						$rowColor = false;

						$html = '<table class="table table-bordered" id="'.$htmlID.'"><tr>';

						foreach($columnNames as $columnName)
						{
							$html .= '<th>'.$columnName.'</th>';
						}

						$html .= '</tr>';

						foreach($selection as $row)
						{
							if(is_array($row) && sizeof($row) > 0)
							{
								if($rowColor)
								{
									$html .= '<tr class="table-secondary">';
								}
								else
								{
									$html .= '<tr class="table-light">';
								}

								foreach($row as $value)
								{
									$html .= "<td>$value</td>";
								}

								$rowColor = !$rowColor;

								$html .= "</tr>";
							}
							else
							{
								return NULL;
							}
						}

						$html .= '</table>';

						return $html;
					}
					else
					{
						return NULL;
					}
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				return NULL;
			}
		}

		// Generates a string which gives the PHP SQL insertion one or more criterias which values are allowed or not before transmission.
		// The syntax of such criterias are %CRITERIA%, e.g. if a database column is not nullable means %NOTNULL%.
		// The criterias are removed by PHP before the SQL statement gets transmitted to the database.
		/*
		private function getInsertionCriterias($tableName, $columnName)
		{
			$isNullable = $this->isNullable($tableName, $columnName);

			$insertionCriterias = '';

			if (!$isNullable) {
				$insertionCriterias .= '%NOTNULL%';
			}

			// Return safe SQL string.
			return $insertionCriterias;
		}
		*/

		/**
		 * Tells you if a SQL record exists already in the given table with the passed values.
		 * The values passed are interpreted in ascending order, meaning
		 * the first value is the first column of the table, the second value the second column and so on...
		 * If you exceed the actual amount of columns with more values passed,
		 * this will not let the function fail (too many arguments to the SQL columns are just ignored then).
		 *
		 * @param string $tableName Name of the table.
		 * @param string ...$columnValues Values of the columns.
		 * @return bool True if exists, otherwise false. Also returns false if it was technically impossible to detect reliably.
		 */
		public function isDuplicateRecord(string $tableName, string ...$columnValues): bool
		{
			$whereSQL = "";

			$columnNames = $this -> getColumnNames($tableName);

			// Because of ID column the expression is "sizeof(..) > 1".
			if(is_array($columnNames) && sizeof($columnNames) > 1)
			{
				for($i = 1; $i < sizeof($columnNames) && $i <= sizeof($columnValues); $i++)
				{
					$columnName = $columnNames[$i];
					$columnValue = $columnValues[$i - 1];

					if($i < (sizeof($columnNames) - 1) && $i < sizeof($columnValues))
					{
						$whereSQL .= "$columnName = '$columnValue' AND ";
					}
					else
					{
						$whereSQL .= "$columnName = '$columnValue'";
					}
				}

				$query = $this -> select("COUNT(1) AS IsExisting", $tableName, $whereSQL, NULL);

				if(is_array($query) && sizeof($query) > 0)
				{
					return $query[0]['IsExisting'];
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Returns a number of columns (names) of a table which are marked as UNIQUE.
		 * Such unique columns must not repeat the values it contains, so there is always exactly one value which is only identical with itself.
		 *
		 * @param string $tableName SQL table name.
		 *
		 * @return array|NULL Column names of those which must contain unique values. If there are no such UNIQUE columns, NULL is returned.
		 */
		public function getUniqueColumnNames(string $tableName): ?array
		{
			$indexesResult = $this -> show("INDEXES", $tableName, "NOT Key_name = 'PRIMARY' AND Non_unique = 0", NULL);

			$uniqueColumns = [];

			if(is_array($indexesResult) && sizeof($indexesResult) > 0)
			{
				foreach($indexesResult as $row)
				{
					$uniqueColumns[] = $row['Column_name'];
				}

				if(sizeof($uniqueColumns) > 0)
				{
					return $uniqueColumns;
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Counts how many identical values exist in a column of a table (by using OR connection for each value specified).
		 *
		 * @param string $tableName SQL table name.
		 * @param string $columnName SQL column name.
		 * @param string ...$columnValues Values to count in a column.
		 *
		 * @return int|NULL Either the amount of values found or NULL if the SQL execution failed.
		 */
		public function countOR(string $tableName, string $columnName, string ...$columnValues): ?int
		{
			$whereSQL = "";

			$size = sizeof($columnValues);

			for($i = 0; $i < $size; $i++)
			{
				$columnValue = $columnValues[$i];

				if($i > 0)
				{
					if($i < ($size - 1))
					{
						$whereSQL .= " OR $columnName = '$columnValue'";
					}
					else
					{
						$whereSQL .= " OR $columnName = '$columnValue';";
					}
				}
				else
				{
					$whereSQL .= "$columnName = '$columnValue'";
				}
			}

			return $this -> selectFirst("COUNT($columnName)", $tableName, $whereSQL);
		}

		/**
		 * Counts how many linked values exist in a column of a table (by using AND connection for each value specified).
		 *
		 * @param string $tableName SQL table name.
		 * @param string $columnName SQL column name.
		 * @param string ...$columnValues Values to count in a column.
		 *
		 * @return int|NULL Either the amount of values found (an integer) or NULL if the SQL execution failed.
		 */
		public function countAND(string $tableName, string $columnName, string ...$columnValues): ?int
		{
			$whereSQL = "";

			$size = sizeof($columnValues);

			for($i = 0; $i < $size; $i++)
			{
				$columnValue = $columnValues[$i];

				if($i > 0)
				{
					if($i < ($size - 1))
					{
						$whereSQL .= " AND $columnName = '$columnValue'";
					}
					else
					{
						$whereSQL .= " AND $columnName = '$columnValue';";
					}
				}
				else
				{
					$whereSQL .= "$columnName = '$columnValue'";
				}
			}

			return $this -> selectFirst("COUNT($columnName)", $tableName, $whereSQL);
		}

		/**
		 * Tells you if a potential record for a table will fulfill all UNIQUE constraints for the related columns.
		 * The values passed are interpreted in ascending order, meaning
		 * the first value is the first column of the table, the second value the second column and so on...
		 * If you exceed the actual amount of columns with more values passed,
		 * this will not let the function fail (too many arguments to the SQL columns are just ignored then).
		 *
		 * @param string $tableName Name of the table.
		 * @param string ...$columnValues Values of the columns.
		 * @return bool Either true (if it fulfills UNIQUE) or false (is also returned when counting fails due to SQL or DB error).
		 */
		public function isUniqueRecord(string $tableName, string ...$columnValues): bool
		{
			$fetchedUniqueColumnNames = $this -> getUniqueColumnNames($tableName);

			if(is_array($fetchedUniqueColumnNames))
			{
				$uniqueColumnNames = array_values($fetchedUniqueColumnNames);

				$fetchedColumnNamesWithoutID = $this -> getColumnNamesWithoutID($tableName);

				if(is_array($fetchedColumnNamesWithoutID))
				{
					$columnNamesIndexed = array_values($fetchedColumnNamesWithoutID);

					for($i = 0; $i < sizeof($columnNamesIndexed) && $i < sizeof($columnValues) && $i < sizeof($uniqueColumnNames); $i++)
					{
						if(strcmp($columnNamesIndexed[$i], $uniqueColumnNames[$i]) == 0)
						{
							// Check if the given value in the current column fulfills UNIQUE.

							// To do so, count all values in the current column:
							$duplicates = $this -> countOR($tableName, $columnNamesIndexed[$i], $columnValues[$i]);

							// See if there are duplicates and if so, return false (potential record will not fulfill UNIQUE!).
							if(is_numeric($duplicates) && $duplicates > 0)
							{
								return false;
							}
						}
					}

					// The potential record will fulfill all UNIQUE constraints.
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}

		/**
		 * Updates a table with the specified parameters.
		 *
		 * @param string $tableName SQL table name.
		 * @param string $updateColumnName SQL UPDATE table name.
		 * @param string $updateValue SQL UPDATE value.
		 * @param string|NULL $where SQL UPDATE condition (without WHERE keyword!).
		 * @return bool Either true if the SQL execution was successful or false if not.
		 */
		public function update(string $tableName, string $updateColumnName, string $updateValue, ?string $where): bool
		{
			$updateSQL = "UPDATE $tableName SET $updateColumnName = '$updateValue'";

			if($where != NULL)
			{
				$updateSQL .= " WHERE $where;";
			}
			else
			{
				$updateSQL .= ";";
			}

			return $this -> perform($updateSQL);
		}

		/**
		 * Inserts an amount of values to the specified table (and saves relevant insertion criteria in $_SESSION['insert(TableName)Criteria']).
		 * This function will not fail if the amount of values
		 * does not match the actual amount of columns in a table.
		 * The insertion works in a sorted order,
		 * meaning the first insertion table (= first parameter)
		 * is also the first column.
		 * Hence, best practice is it to fill in all possible values (and just give default values for unnecessary columns).
		 *
		 * @param $tableName string SQL Table name.
		 * @param ...$insertionValues string SQL values to insert.
		 * @return bool|NULL Either true if the insertion was successful or false if the record violates against UNIQUE constraints in the specified table (record exists already) or NULL if the SQL execution failed / function became corrupted.
		 */
		public function insert(string $tableName, string ...$insertionValues): ?bool
		{
			$sqlInsert = $this -> getInsertionSQL($tableName, ...$insertionValues);

			if(!is_null($sqlInsert))
			{
				$columnNames = $this -> getColumnNamesWithoutID($tableName);

				// Make sure the execution was successful and will not let the function become corrupted.
				if(is_array($columnNames))
				{
					$sqlInsertSuccess = $this -> perform($sqlInsert);

					return $sqlInsertSuccess ? true : NULL;
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * @param $tableName
		 * @return string|NULL
		 */
		private function getPreparedInsertionSQL($tableName) : ?string
		{
			$insertSQL = 'INSERT INTO '.$tableName.'(';

			$columnNames = $this -> getColumnNames($tableName);

			if(is_array($columnNames))
			{
				$size = sizeof($columnNames);

				for($i = 0; $i < $size; $i++)
				{
					$columnName = $columnNames[$i];

					if($i > 0)
					{
						if($i < ($size - 1))
						{
							$insertSQL .= ($columnName.",");
						}
						else
						{
							$insertSQL .= ($columnName.")");
						}
					}

					// Add criterias to the current columns SQL (removed later before transmission).
					$_SESSION['insert'.$columnName.'Criteria'] = $this -> getInsertionCriterias($tableName, $columnName);
				}

				// Half completed insert statement in SQL language.
				// Only mentioned columns for insertion. Values have to follow.
				$insertSQL .= " VALUES (?);";

				return $insertSQL;
			}
			else
			{
				return NULL;
			}
		}

        /**
         * Generates an HTML table,
         * representing
         *
         * @param string $htmlID
         * @param string $tableName
         * @return string|NULL Returns the prepared HTML
         * @author Janek Piekarski
         */
		public function genPostTable(string $htmlID, string $tableName) : ?string
		{
			$columnNames = $this -> getColumnNames($tableName);

			// For later in code
			$insertionSQL = $this -> getPreparedInsertionSQL($tableName);

			if(is_array($columnNames) && sizeof($columnNames) > 0 && is_string($insertionSQL))
			{
				$html = '<table class="table table-bordered mb-3" id="'.$htmlID.'Post">';
				$html .= '<tr>';

				for($i = 0; $i < sizeof($columnNames); $i++)
				{
					$columnName = $columnNames[$i];

					if($i > 0 && $this -> isNullable($tableName, $columnName))
					{
						// Add suffix to the post tables column name.
						// Tells the user whether he has to fill in the field or not (required field or not).
						// If the if-statement is evaluated earlier in this code it will create a bug:
						// the column name is not recognized then anymore (because of suffix added) as it is used to save / remember where to insert the new values.
						// => so do not change this code!
						$columnName .= " (Pflicht)";
					}

					$html .= "<th>$columnName</th>";
				}

				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td class="table-secondary">stetig / ggf. nicht linear</td>';

				for($i = 1; $i < sizeof($columnNames); $i++)
				{
					$cellID = 'insert'.$columnNames[$i];

					// Register insert field in the $_SESSION for valid use.
					$_SESSION[$cellID] = '';

					$html .= '<td><input type="text" name="'.$cellID.'" class="table-light"></td>';
				}

				$html .= '</tr>';
				$html .= '</table>';

				$_SESSION['insert'] = $insertionSQL;

				return $html;
			}
			else
			{
				return NULL;
			}
		}

		// Returns a string which has replaced all relevant special characters with the according equivalents for HTML.
		public function getHTMLSafeValue($sqlQueryString)
		{
			$sqlQueryString = str_replace("'", "&#39;", $sqlQueryString);
			$sqlQueryString = str_replace("\"", "&#34;", $sqlQueryString);

			return $sqlQueryString;
		}

		// Returns an identifier for the internal use of record deletion.
		// Because all actions submitted by a form have to be prepared on the server,
		// you need to prepare a declared PHP $_SESSION variable (for security reasons).
		private function getDeleteActionIdentifier($tableName, $idColumnName, $idColumnValue)
		{
			return 'delete'.$tableName.$idColumnName.$idColumnValue;
		}

		public function getDeleteActionSQLString($tableName, $idColumnName, $idColumnValue)
		{
			return "DELETE FROM $tableName WHERE $idColumnName = $idColumnValue;";
		}

		// Creates an internal (server-side) identifier for the given delete action.
		// The identifier is returned then as a string.
		private function prepareDeleteRecordSQL($tableName, $idColumnName, $idColumnValue)
		{
			$identifier = $this -> getDeleteActionIdentifier($tableName, $idColumnName, $idColumnValue);

			$_SESSION[$identifier] = $this -> getDeleteActionSQLString($tableName, $idColumnName, $idColumnValue);

			return $identifier;
		}

        /**
         * Returns an editable SQL table view from a given SQL selection.
		 * The returned is an HTML form,
		 * ready to use or submit.
         * You are required in this context to give the table a HTML ID (for the case to acccess via JavaScript).
         * The table can be modified by the user.
         * The changes can be sent to the server then.
         * Every field has its own ID (used to update the tables field on the server).
         * Make sure to start a session before using this method.
         * Also make sure, there is a PHP file called "update.php" which handles the entries sent by POST-method.
         *
         * @param array $selection
         * @param string $htmlID
         * @param string $tableName
         * @param string $updatePage
         * @param string $updatePageReturnPage
         * @return string|NULL
         */
		public function genEditTableForm(array $selection, string $htmlID, string $tableName, string $updatePage, string $updatePageReturnPage) : ?string
		{
			if(sizeof($selection) > 0)
			{
				$columnNames = $this -> getColumnNames($tableName);

				// There might be nothing to print from the SQL selection,
				// e.g. because the DB table was not fed with data yet or rows,
				// but it is more important the column names could be fetched
				// (so you can at least add rows to the table)
				if(is_array($columnNames))
				{
					$_SESSION['TableName'] = $tableName;
					$_SESSION['ReturnPage'] = $updatePageReturnPage;

					$html = '<form class="form" action="'.$updatePage.'" method="post">';
					$html .= '<input type="submit" value="Zellen speichern" class="btn btn-primary">';

					if(isset($_GET['action']))
					{
						if(strcmp($_GET['action'], 'success') == 0)
						{
							$html .= '<p class="text-success mb-3">Zellen gespeichert!</p>';
						}
						else
						{
							$html .= '<p class="text-danger mb-3">Vermeiden Sie leere Zellen / interner Fehler!</p>';
						}
					}

					$postTableHTML = $this -> genPostTable($htmlID, $tableName);

					if(is_string($postTableHTML))
					{
						$rowColor = true;

						$html .= '<table class="table table-bordered" id="'.$htmlID.'">';

						$html .= '<tr>';
						for($i = 0; $i < sizeof($columnNames); $i++)
						{
							$columnName = $columnNames[$i];

							if(is_string($columnName))
							{
								$html .= "<th>$columnName";

								if($i == 0 && strcmp($columnName, 'RandomHash') != 0)
								{
									$html .= ' (gesperrt)</th>';
								}

								$html .= '</th>';
							}
						}

						$html .= '<th>Zeile endgültig löschen:</th>';
						$html .= '</tr>';

						foreach($selection as $row)
						{
							$elementIndexed = array_values($row);
							$titleIndexed = $columnNames;

							$idColumnValue = $elementIndexed[0];

							$deleteSQLTagName = $this -> prepareDeleteRecordSQL($tableName, $columnNames[0], $idColumnValue);

							$html .= '<tr>';

							for($i = 0; $i < sizeof($elementIndexed); $i++)
							{
								$currentElementHTML = $this -> getHTMLSafeValue($elementIndexed[$i]);

								$identifier = $titleIndexed[$i];

								// The ID (and RandomHash if given) must not be updated as it is unique (or used by backend system only).
								if($i > 0 && $identifier != 'RandomHash')
								{
									$IDName = $titleIndexed[0];
									$IDValue = $elementIndexed[0];

									$cellSQL = "UPDATE $tableName SET $identifier = \"?\" WHERE $IDName = $IDValue";

									try
									{
										$randomBytes = random_bytes(RANDOM_BYTES_VALUE);
										$cellID = bin2hex($randomBytes);

										// Save SQL statement (only text) in the given cell (represented by random hash).
										// The client does not know about the SQL statement as he only receives a hash in the HTML file.
										$_SESSION[$cellID] = $cellSQL;

										if($rowColor)
										{
											$html .= '<td><textarea cols="20" rows="1" name="'.$cellID.'" class="table-secondary">'.$currentElementHTML.'</textarea></td>';
										}
										else
										{
											$html .= '<td><textarea cols="20" rows="1" name="'.$cellID.'" class="table-light">'.$currentElementHTML.'</textarea></td>';
										}
									}
									catch(Exception $e)
									{
										return NULL;
									}
								}
								else
								{
									$html .= '<td class="table-danger"><span>'.$currentElementHTML.'</span></td>';
								}
							}

							$html .= '<td><input class="form-check-input" type="checkbox" name="'.$deleteSQLTagName.'" class="table-danger"></td>';

							$rowColor = !$rowColor;

							$html .= '</tr>';
						}

						$html .= '</table></form>';

						return $html;
					}
					else
					{
						return NULL;
					}
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Generates a default HTML table to post new values and to edit existing values of the given SQL table.
		 *
		 * @param $tableName string SQL Table name
		 * @param $returnPage string
		 * @return string|NULL
		 */
		public function genDefaultEditTable(string $tableName, string $returnPage) : ?string
        {
			$fetchedTable = $this -> select('*', $tableName, NULL, NULL);

			if(is_array($fetchedTable))
			{
				return $this -> genEditTableForm($fetchedTable, $tableName.'Table', $tableName, DOMAIN.'/home/page/update.php', $returnPage);
			}
			else
			{
				return NULL;
			}
		}
	}
?>
