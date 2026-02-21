<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load HTML tag information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body class="iframeBody">
		<?php
			if(isset($_GET['table']))
			{
                $editTableFormHTML = $essentials -> getDatabaseManager() -> genDefaultEditTable($_GET['table'], DOMAIN.'/home/page/dbview/?table='.$_GET['table']);

                if(is_string($editTableFormHTML))
                {
                    echo $editTableFormHTML;
                }
                else
                {
                    echo '<p class="text-danger">Fehler: hier gibt es nichts zu sehen</p>';
                }
			}
			else
			{
				echo '<h1>Nothing to show. Database view is empty. Is your request (target value in URL is the TableName to look for) valid?</h1>';
			}
		?>
	</body>
</html>
