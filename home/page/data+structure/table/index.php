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
			// Back button
			echo '<button class="button" onclick="window.location=\'', DOMAIN, '/home/page/data+structure/\'">←&nbsp;&nbsp;Zurück</button><br>';

				// Introduction HTML
				echo '<div class="formActionArea"><div class="formActionAreaTitle center bold"><span>Datenbankschema verwalten</span><br></div><div class="formActionAreaDescription"><span>Erstellen oder bearbeiten Sie Informationen für Ihr Datenbankschema.</span><br><br><span class="bold">Beschreibung</span><br><br><span>Um eine neue Spalte zu erstellen, geben Sie mindestens die</span><br><span>dafür relevante Tabelle sowie den Spaltennamen</span><br><span>mit dem entsprechenden SQL-Datentypen an.</span><br></div></div>';

				// Tutorial HTML
				echo '<form action="', DOMAIN, '/home/page/data+structure/table/parse/" method="post"><div class="formActionArea"><div class="formActionAreaTitle center bold"><span>Änderungen anwenden mit Hinweis</span><br></div><br><br><span>Sobald Sie die Änderungen anwenden,</span><br><span>baut das System die Datenbank neu auf.</span><br><span>Ausschlaggebend sind Ihre definierten Tabellen und Spalten.</span><br><span>Jede eingerichtete Tabelle ist, abhängig von der Einstellung,</span><br><span>danach im Menü links anklickbar.</span><br><span>Sie können die Tabellen anschließend manuell bearbeiten</span><br><span>oder mit einer Web-Applikation verbinden.</span><br><span>Alle alten Daten gehen verloren!</span><br><br><br><input type="submit" class="button" value="Änderungen anwenden"><br></div></form>';

				$comboBoxHTML = "";

				if(isset($_POST['targetTable']))
				{
					$comboBoxHTML = $essentials -> getDatabaseManager() -> getReflectionManager() -> genTableHTMLComboBox($_POST['targetTable'], "targetTable");
				}
				else if(isset($_SESSION['targetTable']))
				{
					$comboBoxHTML = $essentials -> getDatabaseManager() -> getReflectionManager() -> genTableHTMLComboBox($_SESSION['targetTable'], "targetTable");
				}
				else
				{
					$comboBoxHTML = $essentials -> getDatabaseManager() -> getReflectionManager() -> genTableHTMLComboBox("", "targetTable");
				}
				
				// Choose table HTML
				echo '<form action="', DOMAIN, '/home/page/data+structure/table/" method="post"><div class="formActionArea"><div class="formActionAreaTitle center bold"><span>Filter anwenden</span><br></div><br><br><span>Tabelle:&nbsp;&nbsp;</span>', $comboBoxHTML, '<br><br><br><input type="submit" class="button" value="Filter anwenden"><br></div><br></form>';

			echo "<br><br>";

			if(isset($_POST['targetTable']))
			{
				if(strcmp($_POST['targetTable'], "All") == 0)
				{
                    echo $essentials -> getDatabaseManager() -> genDefaultEditTable('Reflection', DOMAIN.'/home/page/table/');
				}
				else
				{
                    $essentials -> getDatabaseManager() -> getReflectionManager() -> printColumns($_POST['targetTable']);
				}

				$_SESSION['targetTable'] = $_POST['targetTable'];
			}
			else if(isset($_SESSION['targetTable']))
			{
				if(strcmp($_SESSION['targetTable'], "All") == 0)
				{
                    echo $essentials -> getDatabaseManager() -> genDefaultEditTable('Reflection', DOMAIN.'/home/page/data+structure/table/');
				}
				else
				{
                    $essentials -> getDatabaseManager() -> getReflectionManager() -> printColumns($_SESSION['targetTable']);
				}
			}
			else
			{
                echo $essentials -> getDatabaseManager() -> genDefaultEditTable('Reflection', DOMAIN.'/home/page/data+structure/table/');
			}
		?>
	</body>
</html>
