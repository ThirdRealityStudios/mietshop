<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class GlobalView implements IGlobalView
{
    private IHtmlFactory $htmlFactory;
    private IComponentFactory $componentFactory;

    public function __construct(IHtmlFactory $htmlFactory, IComponentFactory $componentFactory)
    {
        $this->htmlFactory = $htmlFactory;
        $this->componentFactory = $componentFactory;
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

    private function genNavLink(array $appearanceData, string $title, string $href) : string
    {
        return $this->htmlFactory->genNonSelfClosingElement("a", [ "Text" ], $appearanceData, [], [ "nav-link", "darker-interact" ], $title, $href);
    }

    private function genASocialMediaImage(string $socialMediaName, string $href) : string
    {
        return $this->htmlFactory->genNonSelfClosingElement("a", [], [], [], [ "d-inline-block", "me-3", "mb-3", "darker-interact" ], "<img src=\"/img/logo/socialmedia/$socialMediaName.png\" alt=\"$socialMediaName\" title=\"$socialMediaName öffnen\" height=\"50\">", $href);
    }

    private function genContactDataRows(array $companyData, array $appearanceData) : string
    {
        if(empty($companyData))
        {
            return "";
        }

        $streetHTMLSafe = htmlspecialchars($companyData["Street"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $zipCodeHTMLSafe = htmlspecialchars($companyData["ZipCode"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeHTMLSafe = htmlspecialchars($companyData["Place"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $emailHTMLSafe = htmlspecialchars($companyData["Email"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneNormalSafe = htmlspecialchars($companyData["PhoneNormal"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneMobileSafe = htmlspecialchars($companyData["PhoneMobile"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $pContactHint = $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "fs-4" ], "Kontakt:");

        $spanStreet = $this->htmlFactory->genSpanText($appearanceData, $streetHTMLSafe);
        $spanZipPlace = $this->htmlFactory->genSpanText($appearanceData, "{$zipCodeHTMLSafe}&nbsp;{$placeHTMLSafe}");
        $spanEmail = $this->htmlFactory->genSpanText($appearanceData, "Email:&nbsp;");
        $aEmail = $this->htmlFactory->genALink($appearanceData, $emailHTMLSafe, "mailto:{$emailHTMLSafe}");

        $spanPhone = $this->htmlFactory->genSpanText($appearanceData, "Telefon:&nbsp;");
        $aPhone = $this->htmlFactory->genALink($appearanceData, $phoneNormalSafe, "tel:{$phoneNormalSafe}");
        $spanMobile = $this->htmlFactory->genSpanText($appearanceData, "Mobil:&nbsp;");
        $aMobile = $this->htmlFactory->genALink($appearanceData, $phoneMobileSafe, "tel:{$phoneMobileSafe}");

        $contactRows =
        "
        <div class=\"row mb-4\">
            <div class=\"col-12\">
                $pContactHint
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-12\">
                $spanStreet
            </div>
        </div>
        <div class=\"row mb-3\">
            <div class=\"col-12\">
                $spanZipPlace
            </div>
        </div>
        <div class=\"row mb-3\">
            <div class=\"col-12\">
                $spanEmail$aEmail
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-12\">
                $spanPhone$aPhone
            </div>
        </div>
        <div class=\"row mb-3 mb-3\">
            <div class=\"col-12\">
                $spanMobile$aMobile
            </div>
        </div>
        ";

        return $contactRows;
    }

    private function genSocialMediaRow(array $companyData, array $appearanceData) : string
    {
        $facebookURLSafe = htmlspecialchars($companyData["FacebookURL"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $instagramURLSafe = htmlspecialchars($companyData["InstagramURL"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $whatsappURLSafe = htmlspecialchars($companyData["WhatsAppURL"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $kleinanzeigenURLSafe = htmlspecialchars($companyData["KleinanzeigenURL"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $socialMediaButtons = "";
        $socialMediaButtonCount = 0;

        if(!empty($facebookURLSafe))
        {
            $socialMediaButtons .= $this->genASocialMediaImage("Facebook", $facebookURLSafe);
            $socialMediaButtonCount++;
        }

        if(!empty($instagramURLSafe))
        {
            $socialMediaButtons .= $this->genASocialMediaImage("Instagram", $instagramURLSafe);
            $socialMediaButtonCount++;
        }

        if(!empty($whatsappURLSafe))
        {
            $socialMediaButtons .= $this->genASocialMediaImage("WhatsApp", $whatsappURLSafe);
            $socialMediaButtonCount++;
        }

        if(!empty($kleinanzeigenURLSafe))
        {
            $socialMediaButtons .= $this->genASocialMediaImage("Kleinanzeigen", $kleinanzeigenURLSafe);
            $socialMediaButtonCount++;
        }

        $socialMediaRow =
        "
        <div class=\"row mb-5\">
            <span>
                $socialMediaButtons
            </span>
        </div>
        ";

        /** It only makes sense to show a title when there are enough social media links.
         * For only one social media link it is not worth it. */
        if($socialMediaButtonCount > 1)
        {
            $pSocialMediaHint = $this->htmlFactory->genPText($appearanceData, "Alternativ können Sie uns auch auf den gängigen sozialen Medien ansprechen und einen Termin vereinbaren.");
            $pSocialMediaTitle = $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "fs-4" ], "Social Media:");

            $optionalSocialMediaTitleRow =
            "
            <div class=\"row mb-5\">
                $pSocialMediaHint
            </div>
            <div class=\"row mb-3\">
                $pSocialMediaTitle
            </div>
            ";
        }
        else
        {
            $optionalSocialMediaTitleRow = "";
        }

        return $optionalSocialMediaTitleRow.$socialMediaRow;
    }

    private function genCopyrightRow(array $companyData, array $appearanceData) : string
    {
        $companyNameHTMLSafe = htmlspecialchars($companyData["CompanyName"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $copyrightElement = $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "mb-5", "mt-5", "text-center" ], "Copyright&nbsp;©&nbsp;$companyNameHTMLSafe&nbsp;-&nbsp;Alle&nbsp;Rechte&nbsp;vorbehalten.");
        $copyrightRow =
        "
        <div class=\"row mt-5\">
            $copyrightElement
        </div>
        ";

        return $copyrightRow;
    }

    private function genMapsRow(array $companyData) : string
    {
        $mapLocationURLSafe = htmlspecialchars($companyData["MapLocationURL"][0] ?? "", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if(empty($mapLocationURLSafe))
        {
            return "";
        }

        $mapsServiceEmbedded =
        "
        <div class=\"row mb-3\">
            <iframe src=\"$mapLocationURLSafe\" allowfullscreen=\"\" loading=\"lazy\" class=\"rounded bg-white\" style=\"width: 100%;\" height=\"480\"></iframe>
        </div>
        ";

        return $mapsServiceEmbedded;
    }

    private function genDefaultHTMLDoc(array $appearanceData, string $bodyHTML, string $companyName) : string
    {
        $head = $this->genHeadHTML($companyName);

        $bodyContent = $this->htmlFactory->genNonSelfClosingElement("div", [ "Background" ], $appearanceData, [], [], $bodyHTML);
        $body = $this->htmlFactory->genNonSelfClosingElement("body", [ "Window" ], $appearanceData, [], [], $bodyContent);

        $html =
        "
        <!DOCTYPE html>
        <html lang=\"de\">
            $head
            $body
        </html>
        ";

        return $html;
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
            <meta name=\"robots\" content=\"noindex, nofollow\">
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
                
                .darker-interact:hover
                {
                    filter: brightness(90%);
                }

                .darker-interact:active
                {
                    filter: brightness(80%);
                }
		    </style>
        </head>
        ";
    }

    private function genBootstrapHeader(string $companyName, array $appearanceData) : string
    {
        $aLandingPage = $this->genNavLink($appearanceData, "Startseite 🏠", "/");
        $aExplore = $this->genNavLink($appearanceData, "Jetzt Mietobjekte entdecken 🔎", "/?view=search");
        $aAccount = $this->genNavLink($appearanceData, "Konto 👤", "/?view=login");
        $aTermsOfService = $this->genNavLink($appearanceData, "AGBs", "/?view=termsofservice");
        $aPrivacyPolicy = $this->genNavLink($appearanceData, "Datenschutzerklärung", "/?view=privacypolicy");
        $aImprint = $this->genNavLink($appearanceData, "Impressum", "/?view=imprint");

        $sideMenuHeaderTitle = $this->htmlFactory->genNonSelfClosingElement("h5", [ "Text" ], $appearanceData, [], [ "offcanvas-title" ], $companyName, "", "id=\"offcanvasNavbarLabel\"");

        $sideMenuHeaderContent =
            "
        $sideMenuHeaderTitle
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>
        ";

        $sideMenuHeader = $this->htmlFactory->genNonSelfClosingElement("div", [ "Window" ], $appearanceData, [], [ "offcanvas-header" ], $sideMenuHeaderContent);

        $sideMenuBodyContent =
        "
        <ul class=\"navbar-nav justify-content-end flex-grow-1 pe-3\">
            <li class=\"nav-item\">
                $aLandingPage
            </li>
            <li class=\"nav-item\">
                $aExplore
            </li>
            <li class=\"nav-item\">
                $aAccount
            </li>
            <li class=\"nav-item\">
                $aTermsOfService
            </li>
            <li class=\"nav-item\">
                $aPrivacyPolicy
            </li>
            <li class=\"nav-item\">
                $aImprint
            </li>
        </ul>
        ";

        $sideMenuBody = $this->htmlFactory->genNonSelfClosingElement("div", [ "Window" ], $appearanceData, [], [ "offcanvas-body" ], $sideMenuBodyContent);

        $navContent =
        "
        <div class=\"container-fluid p-0\">
            <div class=\"d-flex w-100 p-0 m-0\">
                <div class=\"flex-grow-1 fs-1 ps-4 pt-2 pb-2\">
                    <img src=\"/favicon.png\" alt=\"favicon.png\" style=\"width: auto; height: auto; max-width: 100%; max-height: 2.5em;\">
                </div>
                <div class=\"align-self-center ps-4 pt-2 pe-4 pb-2\">
                    <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasNavbar\" aria-controls=\"offcanvasNavbar\" aria-label=\"Toggle navigation\">
                        <span class=\"navbar-toggler-icon\"></span>
                    </button>
                </div>
            </div>
            <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasNavbar\" aria-labelledby=\"offcanvasNavbarLabel\" role=\"dialog\">
                $sideMenuHeader
                $sideMenuBody
            </div>
        </div>
        ";

        $navBootstrap = $this->htmlFactory->genNonSelfClosingElement("nav", [ "Window", "Frame" ], $appearanceData, [ "border-bottom-style: solid;", "border-bottom-width: 3px;" ], [ "navbar", "sticky-top", "w-100", "z-2" ], $navContent);

        return $navBootstrap;
    }

    /**
     * @param array $companyData Array as described in ICompanyModel by function getCompanyData()
     * @param array $appearanceData Array as described in IAppearanceModel by function getAppearanceData()
     * @return string
     */
    private function genBootstrapFooter(array $companyData, array $appearanceData): string
    {
        $contactDataRows = $this->genContactDataRows($companyData, $appearanceData);
        $socialMediaRows = $this->genSocialMediaRow($companyData, $appearanceData);
        $mapsRow = $this->genMapsRow($companyData);
        $copyrightRow = $this->genCopyrightRow($companyData, $appearanceData);

        $footerContent =
        "
        <div class=\"container-fluid pt-2\">
            $contactDataRows
            $socialMediaRows
            $mapsRow
            $copyrightRow
        </div>
        ";

        $footerContentResponsive = $this->componentFactory->genResponsiveRow($footerContent, [], [], [], []);

        $footer = $this->htmlFactory->genNonSelfClosingElement("div", [ "Window", "Frame" ], $appearanceData, [ "border-top-style: solid; border-top-width: 3px;" ], [ "container-fluid", "pt-5" ], $footerContentResponsive);

        return $footer;
    }

    public function show(array $companyData, array $appearanceData, string $body): void
    {
        // TODO Check if cookies and services accepted. Otherwise, show fullscreen cookie popup.

        $companyNameHTMLSafe = htmlspecialchars($companyData["CompanyName"][0] ?? "Internal Loading Error", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $bodyContainer =
        "
        <div class=\"container-fluid pt-5\" style=\"background-image: url('/img/background.png'); background-repeat: no-repeat; background-size: cover;\">
            $body
        </div>
        ";

        $bodyHTML = $this->genBootstrapHeader($companyNameHTMLSafe, $appearanceData).$bodyContainer.$this->genBootstrapFooter($companyData, $appearanceData);

        echo $this->genDefaultHTMLDoc($appearanceData, $bodyHTML, $companyNameHTMLSafe);
    }
}