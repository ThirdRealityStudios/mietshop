
    <?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/popup/index.php';

    function genPopupDetailPage($serviceName, $publisherName, $publisherDomain, $usedCookies, $publisherPrivacyLink, $acceptPostIdentifier, $currentPageNumber)
		{
      $popupPageDetailHTML = '';

      if(is_string($serviceName) && is_string($publisherName) && is_string($publisherDomain) && is_string($usedCookies) && is_string($publisherPrivacyLink) && is_string($acceptPostIdentifier) && is_numeric($currentPageNumber) && $currentPageNumber > 0)
      {
        $popupPageDetailHTML = '<div class="col-12 mt-3">';

        $popupPageDetailHTML .= '<div class="mb-3">';
        $popupPageDetailHTML .= '<p class="fw-bold mb-0">Dienst:</p>';
        $popupPageDetailHTML .= "<p>$serviceName</p>";
        $popupPageDetailHTML .= '<p class="fw-bold mb-0">Anbieter:</p>';
        $popupPageDetailHTML .= "<p>$publisherName</p>";
        $popupPageDetailHTML .= '<p class="fw-bold mb-0">Domain:</p>';
        $popupPageDetailHTML .= "<p>$publisherDomain</p>";
        $popupPageDetailHTML .= '<p class="fw-bold mb-0">Verwendete(r) Cookie(s):</p>';
        $popupPageDetailHTML .= "<p>$usedCookies</p>";
        $popupPageDetailHTML .= '<p class="fw-bold mb-0">Datenschutzerklärung:</p>';
        $popupPageDetailHTML .= "<a href=\"$publisherPrivacyLink\" target=\"_blank\">$publisherPrivacyLink</a>";
        $popupPageDetailHTML .= '</div>';

        $popupPageDetailHTML .= '<form class="form mb-3" method="POST" action="'.DOMAIN.'/lib/php/popup/cookie/upload/">';

        // Get the default or user-set value for the checkbox (at this point usually always existing - but double safety check only against manipulation)
        if(isset($_SESSION[$acceptPostIdentifier]) && is_numeric($_SESSION[$acceptPostIdentifier]) && $_SESSION[$acceptPostIdentifier] >= 0 && $_SESSION[$acceptPostIdentifier] <= 1)
        {
          $popupPageDetailHTML .= '<label for="'.$acceptPostIdentifier.'" class="form-check-label align-middle me-2">Dienst erlauben:</label>';
          $popupPageDetailHTML .= '<input id="'.$acceptPostIdentifier.'" type="checkbox" name="'.$acceptPostIdentifier.'" class="form-check-input align-middle"';

          if($_SESSION[$acceptPostIdentifier] == 1)
            $popupPageDetailHTML .= ' checked/>';
          else
            $popupPageDetailHTML .= ' />';
        }

        $popupPageDetailHTML .= '<input type="hidden" name="page" value="'.($currentPageNumber+1).'">';
        $popupPageDetailHTML .= '<input type="submit" class="btn btn-primary d-block mt-3" value="Merken und weiter">';
        $popupPageDetailHTML .= '</form>';

        $popupPageDetailHTML .= '<form class="form mb-3" method="POST" action="'.DOMAIN.'/lib/php/popup/cookie/upload/">';
        $popupPageDetailHTML .= '<input type="hidden" name="page" value="0">';
        $popupPageDetailHTML .= '<input type="submit" class="btn btn-secondary" value="Abbrechen">';
        $popupPageDetailHTML .= '</form>';

        $popupPageDetailHTML .= '</div>';
      }
      else
      {
        $popupPageDetailHTML .= '<p class="text-danger">Internal error while generating the requested popup page for the corresponding cookie(s). Are arguments to function invalid?</p>';
      }

      return $popupPageDetailHTML;
    }

    function isServiceCookieValid($cookieName)
    {
      if(is_string($cookieName))
        return (isset($_COOKIE[$cookieName]) && is_numeric($_COOKIE[$cookieName]) && ($_COOKIE[$cookieName] == 0 || $_COOKIE[$cookieName] == 1));

      return false;
    }

    $allowCookiePopup =
    !(isServiceCookieValid('disableAll') || isServiceCookieValid('enableAll') ||
    isServiceCookieValid('enableGoogleMaps') ||
    isServiceCookieValid('enableKleinanzeigen') ||
    isServiceCookieValid('enableInstagram') ||
    isServiceCookieValid('enableWhatsApp') ||
    isServiceCookieValid('enableFacebook'));

    if($allowCookiePopup)
    {
      $popupPageDefaultButtonTitle = 'Alle akzeptieren';
      $popupPageDefaultSubmitValue = 1; // value 1 means: accept all services and cookies (0 means: check what the user wants to be enabled in detail)

      if(isset($_SESSION['isEditingCookieSettings']) && is_numeric($_SESSION['isEditingCookieSettings']) && $_SESSION['isEditingCookieSettings'] == 1)
      {
        $popupPageDefaultButtonTitle = 'Auswahl speichern'; // User was entering the cookie settings and maybe now wants to submit some changes (so adjust the button title for when he comes back to the default page).
        $popupPageDefaultSubmitValue = 0; // User was entering the cookie settings, so tell the PHP server with value 0 to check exactly what the user wants to be enabled.
      }

      $preceedingHTML =
      '
      <p class="fs-1 lh-1 fw-bold mb-3">Cookies und Dienste</p>
      <p class="fs-6 mb-0">Wir verwenden auf unserer Website technische sowie essenzielle Cookies, welche die Grundfunktionen zur einwandfreien Nutzung sicherstellen.</p>
      <p class="fs-6 mb-3">Zusätzlich sind Cookies und Dienste vorzufinden, insbesondere von Drittanbietern, die der Optimierung unserer Dienstleistungen und des Nutzungserlebnisses dienen.</p>';

      $firstAction = DOMAIN.'/lib/php/popup/cookie/upload/';
      $firstHiddenInputName = 'save';
      $firstHiddenInputValue = $popupPageDefaultSubmitValue;
      $firstButtonTitle = $popupPageDefaultButtonTitle;

      $secondAction = $firstAction;
      $secondHiddenInputName = 'page';
      $secondHiddenInputValue = 1;
      $secondButtonTitle = 'Cookies und Dienste ändern';

      $followingHTML = 
      '
      <div>
          <a href="'.DOMAIN.'/imprint" target="_blank" class="small">Impressum</a>
          <a href="'.DOMAIN.'/privacy" target="_blank" class="ms-3 small">Datenschutzerklärung</a>
        </div>
      ';

      $defaultPageCookiePopup = genFullscreenPopup($preceedingHTML.$followingHTML, $firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);

      if(isset($_GET['page']) && is_numeric($_GET['page']))
      {
        switch($_GET['page'])
        {
          // TODO Fetch cookie details from a database table, user-editable for legal reasons at anytime in future.

          case 1:
            // For ease of use and for legal reasons, set every checkbox unchecked by default if the user goes there the first time (to the cookie settings page).
            // Session variables are necessary in order to know later what cookies should be generated or not in order to enable or disable specific services, like Google Maps.
            // The session variables are processed in file popup/cookie/upload/index.php after submitting the form always.
            if(!isset($_SESSION['enableGoogleMaps']))
              $_SESSION['enableGoogleMaps'] = 0;

            echo genFullscreenBlocking(genPopupDetailPage('Google Maps', 'Google Ireland Limited', 'google.com', 'CONSENT, NID, 1P_JAR', 'https://policies.google.com/privacy', 'enableGoogleMaps', 1));
          break;

          case 2:
            if(!isset($_SESSION['enableKleinanzeigen']))
              $_SESSION['enableKleinanzeigen'] = 0;

            echo genFullscreenBlocking(genPopupDetailPage('Kleinanzeigen', 'kleinanzeigen.de GmbH', 'kleinanzeigen.de', 'CSRF-TOKEN, GCLB, POPUPCHECK, clientId, criteo_write_test, cto_pub_test_tld, cto_tld_test, cto_writeable, route_97385225_bd99_4ddb_a1c6_c108f3714d67, up, _ga, _gat, _gid, cto_bundle, cto_pub_test_tld, cto_tld_test, cto_writeable, ekConsentBucketTcf2, ekConsentTcf2, ioam2018, iom_consent, liberty, rbzid, rbzsessionid, i00 (ioam.de), NID (google.com), CONSENT (google.com), 1P_JAR (google.com), IDE (doubleclick.net), uid (criteo.com)', 'https://themen.kleinanzeigen.de/datenschutzerklaerung/', 'enableKleinanzeigen', 2));
          break;

          case 3:
            if(!isset($_SESSION['enableInstagram']))
              $_SESSION['enableInstagram'] = 0;

            echo genFullscreenBlocking(genPopupDetailPage('Instagram', 'Meta Platforms Inc.', 'instagram.com', 'csrftoken, ig_did, mid, fbsr_124024574287414', 'https://de-de.facebook.com/help/instagram/519522125107875', 'enableInstagram', 3));
          break;

          case 4:
            if(!isset($_SESSION['enableWhatsApp']))
              $_SESSION['enableWhatsApp'] = 0;

            echo genFullscreenBlocking(genPopupDetailPage('WhatsApp', 'Meta Platforms Inc.', 'whatsapp.com', 'wa_cb, wa_ul', 'https://www.whatsapp.com/legal/privacy-policy-eea', 'enableWhatsApp', 4));
          break;

          case 5:
            if(!isset($_SESSION['enableFacebook']))
              $_SESSION['enableFacebook'] = 0;

            echo genFullscreenBlocking(genPopupDetailPage('Facebook', 'Meta Platforms Inc.', 'facebook.com', 'rbzid, rbzsessionid, CSRF-TOKEN, GCLB, route_97385225_bd99_4ddb_a1c6_c108f3714d67, up', 'https://www.facebook.com/about/privacy/', 'enableFacebook', 5));
          break;

          default:
            echo $defaultPageCookiePopup;
        }
      }
      else
      {
        echo $defaultPageCookiePopup;
      }
    }
?>