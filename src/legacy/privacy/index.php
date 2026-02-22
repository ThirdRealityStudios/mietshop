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
		?>
		<div class="container-fluid">
			<?php
				$privacyPresetHTMLContent = '<span class="fs-6" style="white-space: pre-line;">';
				
				$privacyPresetHTMLContent .= '<p class="fs-1">Datenschutzerklärung</p>';
				
				$privacyPresetHTMLContent .= '<span class="fw-bold">1. Verantwortlicher:</span>
				
				'.COMPANY_NAME.'
				'.COMPANY_BOSS.'
				'.COMPANY_STREET.'
				'.COMPANY_ZIP.' '.COMPANY_PLACE.'
				Deutschland
				'.EMAIL_IMPRINT.'
				'.COMPANY_PHONE_STATIONARY.'
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">2. Allgemeine Hinweise zur Datenverarbeitung:</span>
				
				Wir nehmen den Schutz Ihrer personenbezogenen Daten sehr ernst und behandeln Ihre personenbezogenen Daten vertraulich sowie entsprechend den gesetzlichen Datenschutzvorschriften (DSGVO) und dieser Datenschutzerklärung.

				Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">3. Erhebung und Speicherung personenbezogener Daten beim Besuch der Website:</span>
				
				Beim Aufrufen dieser Website werden durch den Hosting-Provider automatisch Informationen in sogenannten Server-Logfiles erhoben und gespeichert.

				Dabei können insbesondere folgende Daten verarbeitet werden:

				• IP-Adresse
				• verwendeter Internetbrowser
				• Gerätetyp
				• verwendetes Betriebssystem
				• Hostname des zugreifenden Rechners
				• Datum und Uhrzeit der Serveranfrage

				Diese Daten werden technisch benötigt,
				um:
				
				• einen reibungslosen Verbindungsaufbau der Website zu gewährleisten
				• die Systemsicherheit und -stabilität sicherzustellen
				• die Website technisch zu optimieren
				
				<span class="fw-bold">Rechtsgrundlage</span>: Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse)
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">4. Weitere personenbezogene Daten:</span>
				
				Weitere personenbezogene Daten werden nur verarbeitet, wenn Sie uns diese freiwillig und aktiv mitteilen, z. B. durch:

				• Kontaktanfragen per E-Mail oder Telefon
				• Kontaktformulare
				• Buchungs- oder Mietanfragen
				• Kundenkonten (falls angeboten)
				
				Welche Daten dabei konkret erhoben werden,
				ergibt sich aus dem jeweiligen Formular oder der Kontaktaufnahme.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">5. Zweck der Datenverarbeitung:</span>
				
				Die Verarbeitung personenbezogener Daten erfolgt insbesondere zu folgenden Zwecken:
				
				• Bearbeitung von Anfragen
				• Kommunikation mit Interessenten und Kunden
				• Anbahnung, Durchführung und Abwicklung von Mietverhältnissen
				• Verwaltung von Kundenkonten (falls vorhanden)
				• technische Bereitstellung und Optimierung der Website
				
				<span class="fw-bold">Rechtsgrundlagen können sein:</span>
				
				• Art. 6 Abs. 1 lit. b DSGVO (Vertrag / vorvertragliche Maßnahmen)
				• Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse)
				• Art. 6 Abs. 1 lit. a DSGVO (Einwilligung), sofern erforderlich
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">6. Speicherdauer:</span>
				
				Personenbezogene Daten werden nur so lange gespeichert, wie dies für die jeweiligen Zwecke erforderlich ist oder gesetzliche Aufbewahrungspflichten bestehen.

				Beispielsweise:

				• technische Zugriffsdaten (Server-Logs): zeitlich begrenzt
				• Anfragen: bis zur abschließenden Bearbeitung
				• Vertrags- und Abrechnungsdaten: entsprechend gesetzlicher Aufbewahrungsfristen
				
				Nach Wegfall des Speicherzwecks werden die Daten gelöscht oder anonymisiert.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">7. Weitergabe von Daten an Dritte:</span>
				
				Eine Weitergabe personenbezogener Daten an Dritte erfolgt grundsätzlich nicht, außer:

				• sie ist zur Vertragserfüllung erforderlich
				• sie erfolgt auf Grundlage einer gesetzlichen Verpflichtung
				• Sie haben ausdrücklich eingewilligt

				Sofern externe Dienstleister (z. B. Hosting, Zahlungsanbieter) eingesetzt werden,
				erfolgt dies im Rahmen der gesetzlichen Vorgaben.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">8. Cookies:</span>
				
				Diese Website verwendet Cookies.

				Cookies sind kleine Textdateien, die auf Ihrem Endgerät gespeichert werden und die eine Analyse der Benutzung der Website ermöglichen oder für deren technische Funktion erforderlich sind.

				Es können insbesondere folgende Cookie-Arten eingesetzt werden:

				• technisch notwendige Cookies
				• funktionale Cookies
				• optionale Analyse-Cookies (nur bei entsprechender Nutzung)

				Sie können die Speicherung von Cookies durch eine entsprechende Einstellung Ihres Browsers verhindern.
				Bereits gespeicherte Cookies können jederzeit gelöscht werden.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">9. Optionale externe Dienste:</span>
				
				Je nach Konfiguration dieser Website können externe Dienste eingesetzt werden, z. B.:

				• Google Maps
				• Google Analytics
				• Social-Media-Verlinkungen

				Beim Einsatz solcher Dienste können personenbezogene Daten (z. B. IP-Adresse) an die jeweiligen Anbieter übermittelt werden.

				Die Nutzung dieser Dienste erfolgt nur,
				sofern sie vom Betreiber dieser Website aktiv eingesetzt werden.
				Es gelten die jeweiligen Datenschutzerklärungen der Drittanbieter.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">10. Daten von Kindern:</span>
				
				Diese Website richtet sich nicht gezielt an Personen unter 16 Jahren.
				Personenbezogene Daten von Kindern werden nicht wissentlich verarbeitet.

				Sollten dennoch Daten von Kindern ohne elterliche Zustimmung verarbeitet worden sein,
				bitten wir um einen entsprechenden Hinweis.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">11. Ihre Rechte:</span>
				
				Sie haben im Rahmen der DSGVO insbesondere folgende Rechte:

				• Recht auf Auskunft über Ihre gespeicherten Daten
				• Recht auf Berichtigung unrichtiger Daten
				• Recht auf Löschung Ihrer Daten
				• Recht auf Einschränkung der Verarbeitung
				• Recht auf Datenübertragbarkeit
				• Recht auf Widerruf erteilter Einwilligungen

				Zur Wahrnehmung Ihrer Rechte genügt eine formlose Mitteilung an die oben angegebene Kontaktadresse.
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">12. Antrag auf Löschung personenbezogener Daten:</span>
				
				Sie können die Löschung Ihrer personenbezogenen Daten jederzeit beantragen,
				z. B. per E-Mail an:
				
				'.EMAIL_IMPRINT.'
				
				<span class="d-inline-block w-100 border border-dark"></span>
			
				<span class="fw-bold">13. Stand der Datenschutzerklärung:</span>
				
				'.date("m.Y").'
				
				';
					
				$privacyPresetHTMLContent .= '</span>';
				
				$privacyPresetHTMLContent .= '<a class="btn btn-primary mb-3" href="'.DOMAIN.'">Zur Startseite</a>';
				
				$essentials -> getViewFactory() -> echoFeed(NULL, $privacyPresetHTMLContent);
			?>

		</div>
		<?php
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
