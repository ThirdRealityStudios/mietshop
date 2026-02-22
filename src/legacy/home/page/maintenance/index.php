<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load head-tag information and its configuration.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body class="bg-body-secondary">
		<p class="fs-6 fw-bold">Hier sehen Sie Möglichkeiten zur Wartung Ihrer Datenbank (z.B. neu einrichten):</p>
		<form action="update.php" method="post">
			<?php
				// Contains all table names from tables which are still alive in the database and created by the user.
				$tableNames = $essentials -> getDatabaseManager() -> getParser() -> getAliveTableNames();

				$deleteHTML = <<<HTML

				<div class="formActionArea">
					<div class="formActionAreaTitle center bold">
						<span class="fw-bold">Einzelne</span><span> Tabelleninhalte löschen (Soft Reset):</span><br>
					</div>
					<div class="formActionAreaDescription">
				HTML;

				$deleteHTML .=
				<<<HTML
						<span class="bold">Achtung!</span><br>
						<br>
						<span>=> Alle Daten innerhalb der ausgewählten Tabellen</span><br>
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;gehen unter dieser Aktion verloren.</span><br>
						<br>
						<span>Dies bedeutet,</span><br>
						<span>dass Ihre erstellten Tabellen bestehen bleiben,</span><br>
						<span>die darin gespeicherten Daten jedoch nicht.</span><br>
						<span>Diese Aktion ist gleichzusetzen mit einem "einfachen Reset".</span><br>
						<br>
						<div class="left">
						<div class="formCheckboxArea">
				HTML;

                if($tableNames != NULL)
                {
                    // Print formatted checkboxes in HTML.
                    for($index = 0; $index < sizeof($tableNames); $index++)
                    {
                        $deleteHTML .= "<label for=\"".$tableNames[$index]."\">".$tableNames[$index]." löschen: </label><input type=\"checkbox\" id=\"".$tableNames[$index]."\" name=\"".$tableNames[$index]."\" class=\"checkbox formCheckbox\"><br>";

                        if($index < (sizeof($tableNames) - 1))
                        {
                            $deleteHTML .= "<br>";
                        }
                    }
                }
                else
                {
                    $deleteHTML .= "<span>→ keine Tabellen sind derzeit vorhanden.</span><br>";
                }

				$deleteHTML .=
				<<<HTML
									</div>
								</div>
							</div>
						</div><br>
				HTML;

				$setupHTML = <<<HTML

				<div class="formActionArea">
					<div class="formActionAreaTitle center bold">
						<span>Benutzerdefinierte Tabellen neuinstallieren (Hard Reset):</span><br>
					</div>
					<div class="formActionAreaDescription">
						<span>Mit dieser Aktion richten Sie Ihre benutzerdefinierten Tabellen neu ein,</span><br>
						<span>die Sie unter "Datenstrukturen" → "Tabellen aufrufen" definiert haben.</span><br>
						<br>
						<span class="bold">Achtung!</span><br>
						<br>
						<span>Mit dieser Aktion gehen ausdrücklich ALLE Daten verloren,</span><br>
						<span>die Sie in Ihren eigenen Tabellen gespeichert haben.</span><br>
						<span>Ebenfalls ist es möglich, dass bestimmte Tabellen,</span><br>
						<span>je nach Datenbankschema, nicht mehr existieren oder eine</span><br>
						<span>veränderte Struktur haben.</span><br>
						<span>Alle Tabellen werden jedoch vom System so eingerichtet,</span><br>
						<span>wie sie vom Nutzer vorgesehen sind.</span><br>
						<br>
						<label for="hardreset">Aktion ausführen:</label>
						<input type="checkbox" id="hardreset" name="hardreset" class="checkbox formCheckbox">
					</div>
				</div><br>

				HTML;

				$deleteAllHTML =

				<<<HTML
				<div class="formActionArea">
					<div class="formActionAreaTitle center bold">
						<span class="fw-bold">Alle</span><span> Tabelleninhalte löschen (Soft Reset):</span><br>
					</div>
					<div class="formActionAreaDescription">
						<span class="bold">Achtung!</span><br>
						<br>
						<span>=> Alle Daten innerhalb der benutzerdefinierten Tabellen</span><br>
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;gehen unter dieser Aktion verloren.</span><br>
						<br>
						<span>Dies bedeutet,</span><br>
						<span>dass Ihre erstellten Tabellen bestehen bleiben,</span><br>
						<span>die darin gespeicherten Daten jedoch nicht.</span><br>
						<span>Diese Aktion ist gleichzusetzen mit einem "einfachen Reset".</span><br>
						<br>
						<br>
						<span>Die folgenden Tabellen sind betroffen:</span><br>
						<br>
				HTML;

				if($tableNames != NULL)
				{
						foreach($tableNames as $element)
						{
								$deleteAllHTML.="<span>→ $element</span><br>";
						}
				}
				else
				{
						$deleteAllHTML.="<span>→ keine Tabellen sind derzeit vorhanden.</span><br>";
				}

				$deleteAllHTML.=
				<<<HTML
						<br>
						<label for="delete">Aktion ausführen:</label>
						<input type="checkbox" id="delete" name="delete" class="checkbox formCheckbox">
					</div>
				</div><br>
				HTML;

				echo $deleteHTML;
				echo $deleteAllHTML;

				echo $setupHTML;
			?>

			<br>
			<br>
			<input type="submit" value="Aktion(en) übermitteln" class="btn btn-primary">
		</form>
	</body>
</html>
