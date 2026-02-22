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
	<body>
		<?php
			// Back button
			echo '<button class="btn btn-primary" onclick="window.location=\'', DOMAIN, '/home/page/data+structure/\'">←&nbsp;&nbsp;Zurück</button><br>';
		?>

        <div class="formActionArea">
            <div class="formActionAreaTitle center bold">
                <span>Administrator-Menü organisieren</span><br>
            </div>
            <div class="formActionAreaDescription">
                <span>Legen Sie fest, wie und welche Tabellen</span><br>
                <span>in der Menüansicht links erscheinen sollen.</span><br>
                <br>
                <span class="bold">Beschreibung</span><br>
                <br>
                <span>Um einen neuen Menüpunkt zu erstellen, geben Sie mindestens die</span><br>
                <span>dafür relevante Tabelle sowie den Spaltennamen</span><br>
                <span>mit dem entsprechenden SQL-Datentypen an.</span><br>
            </div>
        </div>

        <br><br><br>

		<?php
            echo $essentials -> getDatabaseManager() -> genDefaultEditTable('HomeAction', DOMAIN.'/home/page/data+structure/menu');
		?>
	</body>
</html>
