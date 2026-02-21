<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';
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
				// Table HTML
				echo '<div class="formActionArea"><div class="formActionAreaTitle center bold"><span>Datenbankschema verwalten</span><br></div><div class="formActionAreaDescription"><span>Erstellen oder bearbeiten Sie Informationen für Ihr Datenbankschema.</span><br><br><span class="bold">Achtung!</span><br><br><span>=> Unüberlegte Änderungen können zu nicht kalkulierbaren Folgen führen</span><br><span>(essentiell für Ihre Web-Applikationen ist dies der Fall).</span><br><span>Beispiele: totaler Datenverlust, Datenleck oder</span><br><span>Unerreichbarkeit einzelner bis aller Anwendungen.</span><br><br><button class="button" onclick="window.location=\'', DOMAIN, '/home/page/data+structure/table\'">Tabellen aufrufen</button><br></div></div><br>';

				// Organize admin menu HTML
				echo '<div class="formActionArea"><div class="formActionAreaTitle center bold"><span>Administrator-Menü organisieren</span><br></div><div class="formActionAreaDescription"><span>Legen Sie fest, wie und welche Tabellen in</span><br><span>der Menüansicht links erscheinen sollen.</span><br><br><button class="button" onclick="window.location=\'', DOMAIN, '/home/page/data+structure/menu\'">Menü organisieren</button><br></div></div><br>';
			?>
	</body>
</html>