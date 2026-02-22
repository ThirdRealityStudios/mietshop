<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/limits/index.php';

	class HomeActionManager
	{
		private DatabaseManager $databaseManager;

		public function __construct($databaseManager)
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

		// Sets up the Reflection table (if missing) for all registered tables by the user.
		function initDatabase() : bool
		{
			$sql = "

			CREATE TABLE IF NOT EXISTS HomeAction
			(
			  	HomeActionID INT(8) AUTO_INCREMENT PRIMARY KEY,

				TableName VARCHAR(64) NOT NULL,
				TargetPropertyName VARCHAR(64) NOT NULL,
				TableAlias VARCHAR(64) NOT NULL
			);

			";

			return $this -> getDatabaseManager() -> perform($sql);
		}

		function getTableNames()
		{
			$unparsedArray = $this -> getDatabaseManager() -> select('TableName', 'HomeAction', NULL, NULL);

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

		function getTargetProperty($tableName)
		{
			return $this -> getDatabaseManager() -> selectFirst('TargetPropertyName', 'HomeAction', "TableName = '$tableName'");
		}

		function getTableAlias($tableName)
		{
			return $this -> getDatabaseManager() -> selectFirst('TableAlias', 'HomeAction', "TableName = '$tableName'");
		}

		function getTableLink($tableName)
		{
			return $this -> getDatabaseManager() -> selectFirst('TableLink', 'HomeAction', "TableName = '$tableName'");
		}

		function getTableByTarget($target)
		{
			$tables = $this -> getTableNames();

			foreach($tables as $table)
			{
				if($this -> getTargetProperty($table) == $target)
				{
					return $table;
				}
			}

			return NULL;
		}

		function isTargetOnMenu($target)
		{
			$tables = $this -> getTableNames();
			
			if($tables !== NULL)
			{
				foreach($tables as $table)
				{
					if($this -> getTargetProperty($table) == $target)
					{
						return 1;
					}
				}
			}

			return 0;
		}
	}
?>
