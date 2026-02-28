<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;

class GlobalView implements IGlobalView
{
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

    /**
     * Generates a pre-defined Bootstrap feed / post in HTML,
     * which has an optional image you can display (150px by 150px) and an HTML description
     * (any HTML of your choice theoretically but be careful what you want to embed as HTML code).
     * IMPORTANT: The existence of the given image URL is not checked!
     * This function will return the feed HTML for use on your webpage.
     * Only make sure you place the function inside a Bootstrap container like this:
     *
     * <div class="container-fluid"><?= genFeed(..); ?></div>
     *
     * @param string|null $pictureURL Simply the URL to your picture which should be displayed in the post. Otherwise pass NULL here (then no picture generated / embedded).
     * @param string $descriptionHTML The HTML code you want to show in your feed (usually formatted text, a button etc.).
     * @return string Feed HTML
     */
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

    private function genBootstrapHeader(string $host, string $companyName) : string
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

    private function genHeadHTML($host, $companyName)
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

    private function genDefaultHTMLDoc(string $bodyHTML, string $host, string $companyName)
    {
        return "<!DOCTYPE html><html lang=\"de\">{$this->genHeadHTML($host, $companyName)}<body class=\"bg-body-secondary\">$bodyHTML</body></html>";
    }

    private function genBootstrapFooter(string $companyName, string $street, string $zipCode, string $place, string $email, string $phoneNormal, string $phoneMobile, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL): string
    {
        $contactCardText = "<p class=\"fs-4\">Kontakt:</p><br>{$street}<br>{$zipCode}&nbsp;{$place}<br><br>E-Mail:&nbsp;<a href=\"mailto:{$email}\" class=\"card-link\" title=\"E-Mail an uns senden\">$email</a><br><br>Telefon:&nbsp;<a href=\"tel:{$phoneNormal}\" class=\"card-link\" title=\"Uns anrufen\">$phoneNormal</a><br>Mobil:&nbsp;<a href=\"tel:$phoneMobile\" class=\"card-link\" title=\"Uns anrufen\">$phoneMobile</a>";

        $contactFeed = $this->genFeed(null, $contactCardText);

        $socialMedia = "";

        $socialMediaCount = 0;

        if(!empty($kleinanzeigenURL))
        {
            $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$kleinanzeigenURL\"><img src=\"/img/logo/socialmedia/Kleinanzeigen.png\" alt=\"Kleinanzeigen\" title=\"Kleinanzeigen öffnen\" height=\"50\"></a>";
            $socialMediaCount++;
        }

        if(!empty($instagramURL))
        {
            $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$instagramURL\"><img src=\"/img/logo/socialmedia/Instagram.png\" alt=\"Instagram\" title=\"Instagram öffnen\" height=\"50\"></a>";
            $socialMediaCount++;
        }

        if(!empty($whatsappURL))
        {
            $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$whatsappURL\"><img src=\"/img/logo/socialmedia/Whatsapp.png\" alt=\"WhatsApp\" title=\"WhatsApp öffnen\" height=\"50\"></a>";
            $socialMediaCount++;
        }

        if(!empty($facebookURL))
        {
            $socialMedia .= "<a class=\"d-inline-block me-2 mb-2\" href=\"$facebookURL\"><img src=\"/img/logo/socialmedia/Facebook.png\" alt=\"Facebook\" title=\"Facebook öffnen\" height=\"50\"></a>";
            $socialMediaCount++;
        }

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

        if(!empty($mapLocationURL))
        {
            $mapsServiceEmbedded = $this->genFeed(null, "<iframe src=\"$mapLocationURL\" allowfullscreen=\"\" loading=\"lazy\" class=\"rounded\" style=\"width: 100%;\" height=\"480\"></iframe>");
        }
        else
        {
            $mapsServiceEmbedded = "<div style=\"height: 20vh;\"></div>";
        }

        if($socialMediaCount <= 1 && empty($mapLocationURL)) // then need distance to the copyright text.
        {
            $socialMedia .= "<div style=\"height: 25vh;\"></div>";
        }

        $socialMediaFeed = $this->genFeed(null, $optionalSocialMediaTitle.$socialMedia);

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
        $bodyHTML = $this->genBootstrapHeader($host, $companyName).$body.$this->genBootstrapFooter($companyName, $street, $zipCode, $place, $email, $phoneNormal, $phoneMobile, $mapLocationURL, $facebookURL, $instagramURL, $whatsappURL, $kleinanzeigenURL);

        echo $this->genDefaultHTMLDoc($bodyHTML, $host, $companyName);
    }
}