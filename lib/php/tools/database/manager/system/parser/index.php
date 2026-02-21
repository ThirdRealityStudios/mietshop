<?php
	class Parser
	{
		private DatabaseManager $databaseManager;

		public function __construct(DatabaseManager $databaseManager)
		{
			$this->databaseManager = $databaseManager;
		}

		/**
		 * @return DatabaseManager
		 */
		private function getDatabaseManager(): DatabaseManager
		{
			return $this->databaseManager;
		}

		// Sets up the SQLCreate table (if missing) to create an SQL statement for each table registered in the Reflection table.
		public function initDatabase() : bool
		{
			$sql = "

			CREATE TABLE IF NOT EXISTS SQLCreate
			(
				SQLCreateID INT(8) AUTO_INCREMENT PRIMARY KEY,

				TableName VARCHAR(64) NOT NULL UNIQUE,
				IfNotExists VARCHAR(2048) NOT NULL
			);

			";

			return $this -> getDatabaseManager() -> perform($sql);
		}

		public function generateSQLLineCreateTable($considerIfNotExists, $tableName)
		{
			return "CREATE TABLE".($considerIfNotExists ? " IF NOT EXISTS " : " ").$tableName;
		}

		public function generateSQLClosedBlock($content)
		{
			return "($content);";
		}

		public function generateSQLLineColumnID($idName, $datatype, $isAutoincrement)
		{
			return "$idName $datatype".($isAutoincrement ? " AUTO_INCREMENT " : " ")."PRIMARY KEY";
		}

		public function generateSQLLineColumnCustomized($columnName, $datatype, $defaultValue, $IsUnique, $isNotNull)
		{
			return "$columnName $datatype".($defaultValue != NULL ? (" DEFAULT '".$defaultValue."'") : "").($isNotNull ? (" NOT NULL") : "").($IsUnique ? (" UNIQUE") : "");
		}

		// Generates a SQL block, containing multiple columns for use in table creation statement.
		public function generateSQLBlockColumnsCustomized($columnsArray)
		{
			$sql = "";

			$isRunning = false;

			foreach($columnsArray as $column)
			{
				if($isRunning)
				{
					$sql .= ", ";
				}

				$isRunning = true;

				$sql .= $this -> generateColumnFromArray($column);
			}
		}

		public function generateSQLBlockCreateTable($tableName, $columnsSQL)
		{
			$sql = $this -> generateSQLLineCreateTable(true, $tableName);

			$columnsSQLPrepared = $this -> generateSQLLineColumnID("$tableName".'ID', "INT(8)", true);

			foreach($columnsSQL as $columnSQL)
			{
				$columnsSQLPrepared .= ", ";

				$columnsSQLPrepared .= $columnSQL;
			}

			$sql .= $this -> generateSQLClosedBlock($columnsSQLPrepared);

			return $sql;
		}

		public function getSampleSQLLineCreateTable()
		{
			return $this -> generateSQLLineCreateTable(true, "Foo").$this -> generateSQLClosedBlock($this -> generateSQLLineColumnID("FooID", "INT(8)", true).','.$this -> generateSQLLineColumnCustomized("ZIP", "INT(8)", "48412", true, true));
		}

		/**
		 * Parses a given table (required, it is known to the Reflection table) to a SQL statement
		 * which can be used to create a table in the database.
		 *
		 * @param $tableName string Table name.
		 * @return string string SQL table creation statement.
		 */
		public function parseTable(string $tableName): string
		{
			$reflectionManager = $this -> getDatabaseManager() -> getReflectionManager();

			$query = $reflectionManager -> getTable($tableName);

			$columnsSQL = array();

			foreach($query as $row)
			{
				$elements = array_values($row);

				$tableName = '';
				$columnName = '';
				$datatype = '';
				$defaultValue = '';
				$isUnique = '';
				$isNotNull = '';

				// Read all Reflection values to the variables (used to generate a SQL column in the table creation statement).
				for($index = 1; $index < sizeof($elements); $index++)
				{
					switch($index)
					{
						case 1:
						{
							$tableName = $elements[1];

							break;
						}
						case 2:
						{
							$columnName = $elements[2];

							break;
						}
						case 3:
						{
							$datatype = $elements[3];

							break;
						}
						case 4:
						{
							$defaultValue = $elements[4];

							break;
						}
						case 5:
						{
							$isUnique = $elements[5];

							break;
						}
						case 6:
						{
							$isNotNull = $elements[6];

							break;
						}
					}
				}

				$columnsSQL[] = $this -> generateSQLLineColumnCustomized($columnName, $datatype, $defaultValue, $isUnique, $isNotNull);
			}

			return $this -> generateSQLBlockCreateTable($tableName, $columnsSQL);
		}

		public function getAliveTableNames()
		{
			$unparsedArray = $this -> getDatabaseManager() -> select('DISTINCT TableName', 'SQLCreate', NULL, NULL);

			if($unparsedArray == NULL)
			{
				return NULL;
			}

			$unparsedArrayKeys = array_values(array_keys($unparsedArray));

			$parsedArray = array();

			foreach($unparsedArrayKeys as $unparsedArrayKey)
			{
				$parsedArray[] = $unparsedArray[$unparsedArrayKey]['TableName'];
			}

			return $parsedArray;
		}

		/**
		 * Parses all Reflection entries to create the corresponding SQL tables.
		 * Requirement: the entries have to be valid in SQL and no error must occur.
		 *
		 * @return void
		 */
		public function parse()
		{
			$reflectionManager = $this -> getDatabaseManager() -> getReflectionManager();

			$tableNames = $reflectionManager -> getTableNames();

			// First create the SQLCreate table if not done yet.
			// It is used to store the CREATE TABLE SQL statement of every table parsed.
			$this -> initDatabase();

			if($tableNames != NULL)
			{
				foreach($tableNames as $tableName)
				{
					$tableCreateSQL = $this -> parseTable($tableName);
					$this -> getDatabaseManager() -> perform($tableCreateSQL);

					$insertCreateSQL = "INSERT INTO SQLCreate (TableName, IfNotExists) VALUES (\"$tableName\", \"$tableCreateSQL\");";
					$this -> getDatabaseManager() -> perform($insertCreateSQL);
				}
			}
		}
	}
?>
