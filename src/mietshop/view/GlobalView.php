<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;

class GlobalView implements IGlobalView
{
    /////////////////////////////////////////////////////////////////////////////////////////////////
    /// Bootstrap Feed And Row Generation Functions

    /**
     * Pre-generates a Bootstrap row in HTML with a top-margin of 5 Bootstrap units (mt-5).
     * So the pre-generated HTML will be returned (NOT outputted).
     * DO NOT use this function outside this PHP file unless you know what you do!
     * Make sure you place the returned HTML code inside a div element of this form
     * (note the correct PHP function call).
     *
     * <div class="container-fluid"><?php echo preGenRow(..); ?></div>
     *
     * @param string $rowContentHTML Simply the HTML code for your Bootstrap row.
     * @param bool $applyTopMargin When 'true', a top-margin of 5 Bootstrap units is applied for the generated row. Otherwise not if 'false'.
     * @param int $leftCol Default value for left Bootstrap column (1 to 12)
     * @param int $leftColXL Value for left Bootstrap column on XL viewport (1 to 12)
     * @param int $leftColXXL Value for left Bootstrap column on XXL viewport (1 to 12)
     * @param int $middleCol Default value for middle Bootstrap column (1 to 12)
     * @param int $middleColXL Value for middle Bootstrap column on XL viewport (1 to 12)
     * @param int $middleColXXL Value for middle Bootstrap column on XXL viewport (1 to 12)
     * @param int $rightCol Default value for right Bootstrap column (1 to 12)
     * @param int $rightColXL Value for right Bootstrap column on XL viewport (1 to 12)
     * @param int $rightColXXL Value for right Bootstrap column on XXL viewport (1 to 12)
     * @return string Row HTML as described.
     */
    private function preGenRow(string $rowContentHTML, bool $applyTopMargin, int $leftCol, int $leftColXL, int $leftColXXL, int $middleCol, int $middleColXL, int $middleColXXL, int $rightCol, int $rightColXL, int $rightColXXL) : string
    {
        $appliedTopMarginCode = ($applyTopMargin) ? ' mt-5' : '';

        // Use of Bootstrap classes for columns here explained:
        // first "col-..." is applied,
        // after that XL viewport (col-xl-...) is fulfilled,
        // and after that the specifications for XXL viewport are used.

        $bootstrapRowString = '<div class="row'.$appliedTopMarginCode.'">';

        if($leftCol > 0 && $leftColXL > 0 && $leftColXXL > 0)
            $bootstrapRowString .= '<div class="col-'.$leftCol.' col-xl-'.$leftColXL.' col-xxl-'.$leftColXXL.'"></div>';

        $bootstrapRowString .= '<div class="col-'.$middleCol.' col-xl-'.$middleColXL.' col-xxl-'.$middleColXXL.'">'.$rowContentHTML.'</div>';

        if($rightCol > 0 && $rightColXL > 0 && $rightColXXL > 0)
            $bootstrapRowString .= '<div class="col-'.$rightCol.' col-xl-'.$rightColXL.' col-xxl-'.$rightColXXL.'"></div>';

        $bootstrapRowString .= '</div>';

        // Returns the pre-generated row HTML code.
        return $bootstrapRowString;
    }

    /**
     * Is the same function as preGenRow(..)
     * but you do NOT need to specify the Bootstrap column parameters anymore.
     * Instead,
     * the default parameters are used,
     * defined in "config.php"
     * (FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE and FEED_COL_XXL_SIDE).
     *
     * @param string $rowContentHTML Simply the HTML code for your Bootstrap row.
     * @param bool $applyTopMargin When 'true', a top-margin of 5 Bootstrap units is applied for the generated row. Otherwise not if 'false'.
     * @return string Row HTML as described.
     */
    private function preGenDefaultRow(string $rowContentHTML, bool $applyTopMargin) : string
    {
        return $this -> preGenRow($rowContentHTML, $applyTopMargin, 1, 3, 4, 10, 6, 4, 1, 3, 4);
    }

    public function genFeed(?string $pictureURL, string $descriptionHTML) : string
    {
        if(is_string($pictureURL))
        {
            $html =
            "
            <div class=\"card container border-1 border-primary bg-primary-subtle\">
                <div class=\"row pt-3 pb-3\">
                    <div class=\"col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-4 align-self-center\">
                        <div class=\"border border-primary rounded bg-light\" style=\"display: flex; justify-content: center; align-items: center; text-align: center; min-width: 120px; width: 120px; min-height: 120px; max-width: 120px; height: 120px;\">
                            <img class=\"img-fluid\" style=\"max-width: 118px; max-height: 118px;\" src=\"$pictureURL\" alt=\"$pictureURL\">
                        </div>
                    </div>
                    <div class=\"col-6 col-sm-8 col-md-9 col-lg-9 col-xl-9 col-xxl-8 align-self-center\">
                        <div>$descriptionHTML</div>
                    </div>
                </div>
            </div>
            ";
        }
        else
        {
            $html =
            "
            <div class=\"card container border-0 bg-primary-subtle\">
                <div class=\"col-md\">
                    <div class=\"card-body\">$descriptionHTML</div>
                </div>
            </div>
            ";
        }

        return $this -> preGenDefaultRow($html, true);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    /// Popup Generation Functions

    /**
     * Generates a (little) transparent fullscreen cover,
     * using Bootstrap colors,
     * which prevents users from interacting with elements from the website.
     * Should be placed as the first element of the body tag,
     * so it can cover everything which follows.
     *
     * @param string $bootstrapFluidContainerHTML The HTML code which fits into a Bootstrap fluid container, e.g. a "<div class="row">...</div>".
     * @return string HTML which represents your blocked screen with your content appearing on it, e.g. cookie popup.
     */
    private function genFullscreenBlocking(string $bootstrapFluidContainerHTML) : string
    {
        return
        "
        <div class=\"container-fluid border border-3 border-primary bg-primary-subtle position-fixed z-3 w-100 h-100\" style=\"opacity: 0.94;\">
            $bootstrapFluidContainerHTML
        </div>
        ";
    }

    /**
     * Internal use for genFormPopup(). Ignore.
     *
     * @param string $firstAction
     * @param string $firstHiddenInputName
     * @param string $firstHiddenInputValue
     * @param string $firstButtonTitle
     * @return string
     */
    private function genFirstPopupForm(string $firstAction, string $firstHiddenInputName, string $firstHiddenInputValue, string $firstButtonTitle) : string
    {
        return
        "
        <form class=\"form mb-3\" method=\"POST\" action=\"$firstAction\">
            <input type=\"hidden\" name=\"$firstHiddenInputName\" value=\"$firstHiddenInputValue\">
            <input type=\"submit\" class=\"btn btn-primary fw-bold d-block mt-3\" value=\"$firstButtonTitle\">
        </form>
        ";
    }

    /**
     * Internal use for genFormPopup(). Ignore.
     *
     * @param string $secondAction
     * @param string $secondHiddenInputName
     * @param string $secondHiddenInputValue
     * @param string $secondButtonTitle
     * @return string
     */
    private function genSecondPopupForm(string $secondAction, string $secondHiddenInputName, string $secondHiddenInputValue, string $secondButtonTitle) : string
    {
        return
        "
        <form class=\"form mb-3\" method=\"POST\" action=\"$secondAction\">
            <input type=\"hidden\" name=\"$secondHiddenInputName\" value=\"$secondHiddenInputValue\">
            <input type=\"submit\" class=\"btn btn-secondary\" value=\"$secondButtonTitle\">
        </form>
        ";
    }

    /**
     * Internal use for genFormPopup(). Ignore.
     *
     * @param string $firstAction
     * @param string $firstHiddenInputName
     * @param string $firstHiddenInputValue
     * @param string $firstButtonTitle
     * @param string $secondAction
     * @param string $secondHiddenInputName
     * @param string $secondHiddenInputValue
     * @param string $secondButtonTitle
     * @return string
     */
    private function genPopupForms(string $firstAction, string $firstHiddenInputName, string $firstHiddenInputValue, string $firstButtonTitle, string $secondAction, string $secondHiddenInputName, string $secondHiddenInputValue, string $secondButtonTitle) : string
    {
        return $this->genFirstPopupForm($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle).$this->genSecondPopupForm($secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);
    }

    /**
     * Generates a (little) transparent fullscreen popup,
     * using pre-defined Bootstrap colors and the parameters to create a form for both buttons.
     * Should be placed as the first element of the body tag,
     * so the popup can cover everything which follows.
     * The parameter description does not have to be related to a cookie consent popup,
     * as it is only an example below.
     * Bootstrap was being used to create the popup HTML.
     * The returned HTML string represents a <div class="container-fluid ...">...</div>.
     * Never use this function to generate multiple popups at once,
     * as there can be only one visible at once which covers the whole screen after the body tag.
     *
     * @param string $preceedingHTML Maybe a title with description, like <p>Cookie consent</p><p>Please select Yes or No.</p>
     * @param string $firstAction For e.g. "https://my-domain.de/cookie/consent/"
     * @param string $firstHiddenInputName E.g. "enableAllServices" for clicking the first button.
     * @param string $firstHiddenInputValue E.g. "1" in order to symbolize "yes, I want all services to be active".
     * @param string $firstButtonTitle E.g. "Yes, activate all services".
     * @param string $secondAction Self-explanatory (it could be like $firstAction in this example).
     * @param string $secondHiddenInputName E.g. "disableAllServices".
     * @param string $secondHiddenInputValue E.g. "1" in order to say "Do it. Disable every service".
     * @param string $secondButtonTitle E.g. "No, disable all services".
     * @return string HTML which represents your fullscreen popup, using Bootstrap fluid container.
     * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
     */
    private function genFullscreenPopup(string $preceedingHTML, string $firstAction, string $firstHiddenInputName, string $firstHiddenInputValue, string $firstButtonTitle, string $secondAction, string $secondHiddenInputName, string $secondHiddenInputValue, string $secondButtonTitle) : string
    {
        $formHTML = $this->genPopupForms($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);

        $popupContentHTML = "<div class=\"row\"><div class=\"col-12 mt-3\">{$preceedingHTML}{$formHTML}</div></div>";

        return $this->genFullscreenBlocking($popupContentHTML);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    /// Cookie And Services Consent Popup Functions

    /**
     * @param string $serviceName
     * @param string $publisherName
     * @param string $publisherDomain
     * @param string $usedCookies
     * @param string $publisherPrivacyLink
     * @param int $currentPageNumber
     * @param string $checkboxName
     * @param bool $isBoxChecked
     * @return string
     */
    function genPopupDetailPage(string $serviceName, string $publisherName, string $publisherDomain, string $usedCookies, string $publisherPrivacyLink, int $currentPageNumber, string $checkboxName, bool $isBoxChecked) : string
    {
        $popupPageDetailHTML = '';

        if($currentPageNumber > 0)
        {
            $popupPageDetailHTML =
            "
            <div class=\"col-12 mt-3\">
                <div class=\"mb-3\">
                    <p class=\"fw-bold mb-0\">Dienst:</p>
                    <p>$serviceName</p>
                    <p class=\"fw-bold mb-0\">Anbieter:</p>
                    <p>$publisherName</p>
                    <p class=\"fw-bold mb-0\">Domain:</p>
                    <p>$publisherDomain</p>
                    <p class=\"fw-bold mb-0\">Verwendete(r) Cookie(s):</p>
                    <p>$usedCookies</p>
                    <p class=\"fw-bold mb-0\">Datenschutzerklärung:</p>
                    <a href=\"$publisherPrivacyLink\" target=\"_blank\">$publisherPrivacyLink</a>
                </div>
                <form class=\"form mb-3\" method=\"POST\" action=\"\">
                    <label for=\"$checkboxName\" class=\"form-check-label align-middle me-2\">Dienst erlauben:</label>
                    <input id=\"$checkboxName\" type=\"checkbox\" name=\"$checkboxName\" class=\"form-check-input align-middle\"
                ";

            if($isBoxChecked)
                $popupPageDetailHTML .= " checked/>";
            else
                $popupPageDetailHTML .= " />";

            $nextPageNumber = $currentPageNumber + 1;

            $popupPageDetailHTML .=
            "
                    <input type=\"hidden\" name=\"page\" value=\"{$nextPageNumber}\">
                    <input type=\"submit\" class=\"btn btn-primary d-block mt-3\" value=\"Merken und weiter\">
                </form>
                <form class=\"form mb-3\" method=\"POST\" action=\"\">
                    <input type=\"hidden\" name=\"page\" value=\"0\">
                    <input type=\"submit\" class=\"btn btn-secondary\" value=\"Abbrechen\">
                </form>
            </div>
            ";
        }

        return $popupPageDetailHTML;
    }



    /////////////////////////////////////////////////////////////////////////////////////////////////
    /// HTML Doc Basic Functions

    private function genBootstrapHeader(string $companyName) : string
    {
        return "
        <nav class=\"navbar bg-primary-subtle sticky-top border-bottom border-3 border-primary w-100 z-2\">
	        <div class=\"container-fluid\">
		        <img src=\"/favicon.png\" alt=\"favicon.png\" width=\"70\" height=\"70\">
                    <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasNavbar\" aria-controls=\"offcanvasNavbar\" aria-label=\"Toggle navigation\">
                        <span class=\"navbar-toggler-icon\"></span>
                    </button>
                    <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasNavbar\" aria-labelledby=\"offcanvasNavbarLabel\" role=\"dialog\">
                        <div class=\"offcanvas-header bg-primary-subtle border-bottom border-3 border-primary\">
                            <h5 class=\"offcanvas-title\" id=\"offcanvasNavbarLabel\">{$companyName}</h5>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>
                        </div>
                        <div class=\"offcanvas-body bg-primary-subtle\">
                            <ul class=\"navbar-nav justify-content-end flex-grow-1 pe-3\">
                                <li class=\"nav-item\">
                                    <a class=\"nav-link active\" aria-current=\"page\" href=\"\">Startseite 🏠</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"/?view=search\">Jetzt Mietobjekte entdecken 🔎</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"/?view=login\">Anmelden</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"/?view=termsofservice\">AGBs</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"/?view=privacypolicy\">Datenschutzerklärung</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"/?view=imprint\">Impressum</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        ";
    }

    private function genHeadHTML($companyName) : string
    {
        return "
        <head>
            <link rel=\"icon\" type=\"image/png\" href=\"/favicon.png\"/>
            <link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\"/>
            <meta charset=\"utf-8\">
            <meta name=\"description\" content=\"{$companyName}\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
            <title>{$companyName}</title>
            <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz\" crossorigin=\"anonymous\"></script>
            <script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>
            <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH\" crossorigin=\"anonymous\">
            <style>
                @keyframes spin
                {
                    from
                    {
                        transform: rotate(0deg);
                    }
                    to
                    {
                        transform: rotate(360deg);
                    }
                }
                
                .gear-spin
                {
                    display: inline-block;
                    animation: spin 1.4s linear infinite;
                }
		    </style>
        </head>
        ";
    }

    private function genDefaultHTMLDoc(string $bodyHTML, string $companyName) : string
    {
        return "<!DOCTYPE html><html lang=\"de\">{$this->genHeadHTML($companyName)}<body class=\"bg-primary-subtle\"><div class=\"bg-body-secondary\">$bodyHTML</div></body></html>";
    }

    private function genBootstrapFooter(string $companyName, string $street, string $zipCode, string $place, string $email, string $phoneNormal, string $phoneMobile, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL): string
    {


        $contactCardText = "<p class=\"fs-4\">Kontakt:</p><br>{$street}<br>{$zipCode}&nbsp;{$place}<br><br>E-Mail:&nbsp;<a href=\"mailto:{$email}\" class=\"card-link\" title=\"E-Mail an uns senden\">$email</a><br><br>Telefon:&nbsp;<a href=\"tel:{$phoneNormal}\" class=\"card-link\" title=\"Uns anrufen\">$phoneNormal</a><br>Mobil:&nbsp;<a href=\"tel:$phoneMobile\" class=\"card-link\" title=\"Uns anrufen\">$phoneMobile</a>";

        $contactFeed = $this->genFeed(null, $contactCardText);

        $socialMedia = "";

        $socialMediaCount = 0;

        $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"{$kleinanzeigenURL}\"><img src=\"/img/logo/socialmedia/Kleinanzeigen.png\" alt=\"Kleinanzeigen\" title=\"Kleinanzeigen öffnen\" height=\"50\"></a>";
        $socialMediaCount++;

        $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$instagramURL\"><img src=\"/img/logo/socialmedia/Instagram.png\" alt=\"Instagram\" title=\"Instagram öffnen\" height=\"50\"></a>";
        $socialMediaCount++;

        $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$whatsappURL\"><img src=\"/img/logo/socialmedia/Whatsapp.png\" alt=\"WhatsApp\" title=\"WhatsApp öffnen\" height=\"50\"></a>";
        $socialMediaCount++;

        $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$facebookURL\"><img src=\"/img/logo/socialmedia/Facebook.png\" alt=\"Facebook\" title=\"Facebook öffnen\" height=\"50\"></a>";
        $socialMediaCount++;

        $optionalSocialMediaTitle = "";

        /** It only makes sense to show a title when there are enough social media links.
         * For only one social media link it is not worth it. */
        if($socialMediaCount > 1)
        {
            $optionalSocialMediaTitle =
            "
            <p class=\"fs-6\">Alternativ können Sie uns auch auf den gängigen sozialen Medien ansprechen und einen Termin vereinbaren.</p>
            <p class=\"fs-4\">Social Media:</p>
            ";
        }

        $socialMediaFeed = $this->genFeed(null, $optionalSocialMediaTitle.$socialMedia);

        $mapsServiceEmbedded = "";

        if(!empty($mapLocationURL))
        {
            $mapsServiceEmbedded = $this->genFeed(null, "<iframe src=\"$mapLocationURL\" allowfullscreen=\"\" loading=\"lazy\" class=\"rounded bg-white\" style=\"width: 100%;\" height=\"480\"></iframe>");
        }

        $copyrightRow = $this->preGenDefaultRow("<p class=\"mb-0 text-center\">Copyright&nbsp;©&nbsp;$companyName&nbsp;-&nbsp;Alle&nbsp;Rechte&nbsp;vorbehalten.</p>", true);

        return "
        <div class=\"container-fluid mt-5 pb-5 bg-primary-subtle border-top border-3 border-primary\">
            $contactFeed
            $socialMediaFeed
            $mapsServiceEmbedded
            $copyrightRow
        </div>
        ";
    }

    public function show(string $host, string $companyName, string $companyOwner, string $street, string $zipCode, string $place, string $email, string $phoneMobile, string $phoneNormal, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL, string $privacyPolicy, string $termsOfService, string $body): void
    {
        $companyNameHTMLSafe = htmlspecialchars($companyName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $streetHTMLSafe = htmlspecialchars($street, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $zipCodeHTMLSafe = htmlspecialchars($zipCode, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeHTMLSafe = htmlspecialchars($place, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $emailHTMLSafe = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneNormalSafe = htmlspecialchars($phoneNormal, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneMobileSafe = htmlspecialchars($phoneMobile, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $mapLocationURLSafe = htmlspecialchars($mapLocationURL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $facebookURLSafe = htmlspecialchars($facebookURL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $instagramURLSafe = htmlspecialchars($instagramURL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $whatsappURLSafe = htmlspecialchars($whatsappURL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $kleinanzeigenURLSafe = htmlspecialchars($kleinanzeigenURL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $bodyHTML = $this->genBootstrapHeader($companyNameHTMLSafe).$body.$this->genBootstrapFooter($companyNameHTMLSafe, $streetHTMLSafe, $zipCodeHTMLSafe, $placeHTMLSafe, $emailHTMLSafe, $phoneNormalSafe, $phoneMobileSafe, $mapLocationURLSafe, $facebookURLSafe, $instagramURLSafe, $whatsappURLSafe, $kleinanzeigenURLSafe);

        echo $this->genDefaultHTMLDoc($bodyHTML, $companyNameHTMLSafe);
    }
}