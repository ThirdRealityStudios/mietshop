<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();

	$tableNames = $essentials -> getDatabaseManager() -> getParser() -> getAliveTableNames();

	if($tableNames != NULL)
	{
			// If set, delete only the given tables.
			foreach($tableNames as $tableName)
			{
					if(isset($_POST["$tableName"]))
					{
							// Empty all tables which are known to the active table list (= SQLCreate).
							$essentials -> getDatabaseManager() -> delete($tableName, NULL);
					}
			}
	}

	if($tableNames != NULL)
	{
			// If set, delete all tables (which were created, according to Reflection).
			if(isset($_POST['delete']))
			{
					foreach($tableNames as $tableName)
					{
							// Empty all tables which are known to the active table list (= SQLCreate).
							$essentials -> getDatabaseManager() -> delete($tableName, NULL);
					}
			}
	}

	if(isset($_POST['hardreset']))
	{
        $essentials -> getDatabaseManager() -> hardreset();
	}

	header("Location: ".DOMAIN."/home/page/maintenance/?action=success");
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
		<span>If you can read this, there must have been an internal error. Please go back and try again later. In case of further issues contact the according the administration.</span>
	</body>
</html>
