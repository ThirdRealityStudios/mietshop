<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/limits/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/config/index.php';
	
	class ReflectionManager
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
			return $this -> databaseManager;
		}

		// Sets up the Reflection table (if missing) for all registered tables by the user.
		public function initDatabase() : bool
		{
			$sql = "

			CREATE TABLE IF NOT EXISTS Reflection
			(
			 	ReflectionID INT(8) AUTO_INCREMENT PRIMARY KEY,

				TableName VARCHAR(64) NOT NULL,
				ColumnName VARCHAR(64) NOT NULL,
				Datatype VARCHAR(64) NOT NULL,
				DefaultValue VARCHAR(64),
				IsUnique BOOL DEFAULT FALSE,
				IsNotNull BOOL DEFAULT FALSE
			);

			";

			return $this -> getDatabaseManager() -> perform($sql);
		}

		/**
		 * @param string $defaultValue
		 * @param string $tables
		 * @return string|NULL Returns the <select> HTML combo box code or NULL if e.g. SQL selection internally fails for all table names.
		 */
		public function genTableHTMLComboBox(string $defaultValue, string $tables) : ?string
		{
			$tableRows = $this -> getDatabaseManager() -> select(' DISTINCT TableName', 'Reflection', null, null);

			if(is_array($tableRows))
			{
				$html = "<select name=\"$tables\">";

				if(strcmp($defaultValue, "All") == 0)
				{
					$html .= "<option value=\"All\" selected>Alle</option>";
				}
				else
				{
					$html .= "<option value=\"All\">Alle</option>";
				}

				foreach($tableRows as $tableElement)
				{
					foreach($tableElement as $tableName)
					{
						$html .= "<option value=\"$tableName\"";
						
						if(strcmp($defaultValue, $tableName) == 0)
						{
							$html .= ' selected';
						}

						$html .= ">$tableName</option>";
					}
				}

				$html .= '</select>';

				return $html;
			}
			else
			{
				return NULL;
			}
		}

		// Prints a list of the columns of the given table.
		public function printColumns($tableName) : void
		{
			$table = $this -> getDatabaseManager() -> select('*', 'Reflection', "TableName = \"$tableName\"", null);

			echo $this -> getDatabaseManager() -> genEditTableForm($table, 'tableTable', 'Reflection', DOMAIN.'/home/page/update.php', DOMAIN.'/home/page/data+structure/table/');

			if(is_null($table))
			{
				echo '<span>Für die gewählte Tabelle existieren noch keine Spalten. Bitte legen Sie diese zuerst an!</span>';
			}
		}

		/**
		 * @return array|null
		 */
		public function getTableNames() : ?array
		{
			$unparsedArray = $this -> getDatabaseManager() -> select('DISTINCT TableName', 'Reflection', NULL, NULL);

			if(is_array($unparsedArray))
			{
				$unparsedArrayKeys = array_values(array_keys($unparsedArray));

				$parsedArray = [];

				foreach($unparsedArrayKeys as $unparsedArrayKey)
				{
					$parsedArray[] = $unparsedArray[$unparsedArrayKey]['TableName'];
				}

				return $parsedArray;
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Returns all entries from the table Reflection as an array which match the given $tableName.
		 *
		 * @param $tableName
		 * @return array|NULL
		 */
		public function getTable($tableName) : ?array
		{
			return $this -> getDatabaseManager() -> select('*', 'Reflection', "TableName = '$tableName'", '');
		}
	}
?>
