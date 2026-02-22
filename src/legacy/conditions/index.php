<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

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
	<body class="bg-body-secondary">
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
			
			$conditionsHTMLContent = '<span class="fs-6" style="white-space: pre-line;">';
			
			$conditionsHTMLContent .= '<p class="fs-1">Allgemeine Geschäftsbedingungen</p>';
			
			// Use preset for conditions page (as long as the user does not want to change it to his own)
			$conditionsHTMLContent .= '<span class="fw-bold">1. Vertragspartner:</span>
			
			Nachfolgend wird der Kunde als <span class="fw-bold">Mieter</span> bezeichnet.
			Vermieter ist:
			
			<span class="fw-bold">'.COMPANY_NAME.'</span>
			'.COMPANY_STREET.'
			'.COMPANY_ZIP.' '.COMPANY_PLACE.'
			vertreten durch '.COMPANY_BOSS.'

			Nachfolgend <span class="fw-bold">Vermieter</span> bezeichnet.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">2. Geltungsbereich:</span>
			
			Diese Allgemeinen Geschäftsbedingungen gelten für sämtliche Mietverträge zwischen dem Vermieter und dem Mieter.
			Abweichende oder ergänzende Bedingungen des Mieters finden keine Anwendung,
			sofern ihrer Geltung nicht ausdrücklich schriftlich zugestimmt wurde.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">3. Vertragsgegenstand und Vertragsabschluss:</span>
			
			Gegenstand des Vertrages sind die im Mietvertrag bzw. in der Rechnung aufgeführten Mietgegenstände einschließlich des vereinbarten Zubehörs.
			Der Mietvertrag kommt durch schriftliche Bestätigung,
			elektronische Bestätigung oder Übergabe der Mietgegenstände zustande.
			
			Zusätzlich vereinbarte <span class="fw-bold">Neben- oder Sonderleistungen</span> (z. B. Lieferung, Auf- oder Abbau, Zusatzartikel) werden gesondert im Mietvertrag oder in der Rechnung ausgewiesen.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">4. Bestimmungsgemäße Nutzung:</span>
			
			Die Mietgegenstände dürfen ausschließlich bestimmungsgemäß und gemäß den überlassenen Gebrauchs- und Sicherheitshinweisen verwendet werden.

			Eine Überlastung,
			unsachgemäße Nutzung,
			Veränderung oder Weitervermietung der Mietgegenstände ist untersagt.
			Bei Unklarheiten bezüglich der Nutzung hat sich der Mieter vor Inbetriebnahme an den Vermieter zu wenden.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">5. Preise und Zahlungsbedingungen:</span>
			
			Alle angegebenen Preise sind <span class="fw-bold">Bruttopreise gemäß § 19 UStG (Kleinunternehmerregelung)</span>.
			
			Die Zahlung ist - sofern nicht anders vereinbart - bei Übergabe der Mietgegenstände in bar oder vorab per Überweisung zu leisten.
			
			Erfolgt die Zahlung nicht zum vereinbarten Zeitpunkt,
			ist der Vermieter berechtigt,
			vom Vertrag zurückzutreten und einen hierdurch entstandenen Schaden geltend zu machen.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">6. Mietzeit und Mietdauer:</span>
			
			Die Mietdauer beginnt mit der Übergabe der Mietgegenstände an den Mieter und endet mit deren vollständiger Rückgabe an den Vermieter.
			Ein Verleihtag umfasst maximal 24 Stunden,
			sofern nichts Abweichendes vereinbart wurde.
			Maßgeblich sind die im Mietvertrag festgelegten Zeitpunkte.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">7. Lieferung und Abholung:</span>
			
			Sofern eine Lieferung oder Abholung vereinbart wurde,
			erfolgt diese zu den im Mietvertrag oder in der schriftlichen Kommunikation festgelegten Konditionen.
			
			Mündliche Nebenabreden sind nicht verbindlich.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">8.Verspätete Rückgabe:</span>
			
			Bei nicht fristgerechter Rückgabe der Mietgegenstände ist der Vermieter berechtigt,
			einen weiteren Miettag zu berechnen.

			Dies gilt nicht,
			wenn der Mieter die Verzögerung rechtzeitig angezeigt hat und ein nachvollziehbarer Grund vorliegt.

			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">9. Rücktritt durch den Mieter:</span>
			
			Ein Rücktritt durch den Mieter ist dem Vermieter unverzüglich mitzuteilen.
			Der Mieter hat hierbei die Planungssicherheit des Vermieters zu berücksichtigen.

			Bereits entstandene Kosten oder Aufwendungen können dem Mieter in Rechnung gestellt werden.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">10. Rücktritt durch den Vermieter:</span>
			
			Der Vermieter ist berechtigt,
			vom Vertrag zurückzutreten,
			sofern Ereignisse eintreten,
			die die ordnungsgemäße Durchführung des Mietvertrages unmöglich oder unzumutbar machen.

			Hierzu zählen insbesondere:

			• Krankheit oder Ausfall des Vermieters
			• höhere Gewalt
			• Diebstahl oder Beschädigung der Mietgegenstände
			• Nichteinhaltung vereinbarter Zahlungsbedingungen
			• ungeeignete Einsatzorte oder Einsatzbedingungen
			• erhebliche Sicherheitsbedenken (z.B. Wetterbedingungen)
			
			Weitergehende Ansprüche des Mieters bestehen in diesen Fällen nicht.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">11. Obhutspflicht und Aufsicht:</span>
			
			Der Mieter ist verpflichtet,
			die Mietgegenstände während der gesamten Mietdauer sorgfältig zu behandeln und ordnungsgemäß zu sichern.

			Sofern Art und Nutzung der Mietgegenstände eine Beaufsichtigung erfordern,
			ist diese durch eine geeignete,
			volljährige und verantwortliche Person sicherzustellen.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">12. Schäden, Verlust und Diebstahl:</span>
			
			Beschädigungen,
			Verlust oder Diebstahl der Mietgegenstände sind dem Vermieter unverzüglich mitzuteilen.

			Der Mieter haftet für Schäden,
			die während der Mietdauer entstehen,
			soweit diese nicht auf normalen Verschleiß zurückzuführen sind.

			Kosten für Reparatur oder Ersatz können dem Mieter bis zur Höhe der Wiederbeschaffungskosten in Rechnung gestellt werden.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">13. Zustand der Mietgegenstände bei Übergabe:</span>
			
			Die Mietgegenstände werden dem Mieter in einem ordnungsgemäßen,
			funktionsfähigen und gereinigten Zustand übergeben.

			Offensichtliche Mängel sind bei Übergabe unverzüglich anzuzeigen.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">14. Haftung:</span>
			
			Der Vermieter haftet unbeschränkt für Schäden aus der Verletzung des Lebens,
			des Körpers oder der Gesundheit,
			die auf Vorsatz oder Fahrlässigkeit beruhen.

			Für sonstige Schäden haftet der Vermieter nur bei Vorsatz oder grober Fahrlässigkeit.
			Bei einfacher Fahrlässigkeit haftet der Vermieter nur bei Verletzung wesentlicher Vertragspflichten und begrenzt auf den vorhersehbaren,
			vertragstypischen Schaden.

			Eine weitergehende Haftung ist ausgeschlossen.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">15. Reinigung bei Rückgabe:</span>
			
			Die Mietgegenstände sind in einem sauberen und ordnungsgemäßen Zustand zurückzugeben.

			Bei außergewöhnlicher Verschmutzung behält sich der Vermieter vor,
			den zusätzlichen Reinigungsaufwand in Rechnung zu stellen.

			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">16. Wetter- und Umwelteinflüsse:</span>
			
			Sofern Mietgegenstände witterungsabhängig sind,
			trägt der Mieter das Risiko für wetterbedingte Einschränkungen,
			sofern nichts anderes vereinbart wurde.
			
			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">17. Kaution:</span>
			
			Für bestimmte Mietgegenstände kann eine Kaution vereinbart werden.
			Die Höhe der Kaution ergibt sich aus dem jeweiligen Mietvertrag.

			<span class="d-inline-block w-100 border border-dark"></span>
			
			<span class="fw-bold">18. Gerichtsstand:</span>
			
			Sofern der Mieter Kaufmann oder Unternehmer im Sinne des HGB ist,
			ist Gerichtsstand der Sitz des Vermieters.
			
			Stand: '.date("m.Y").'
			
			';
			
			$conditionsHTMLContent .= '</span>';
			$conditionsHTMLContent .= '<a class="btn btn-primary mb-3" href="'.DOMAIN.'">Zur Startseite</a>';
		?>
		<div class="container-fluid">
			<?php
				$essentials -> getViewFactory() -> echoFeed(NULL, $conditionsHTMLContent);
			?>
		</div>

		<?php
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
