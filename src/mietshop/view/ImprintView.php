<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IImprintView;

class ImprintView implements IImprintView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData, array $appearanceData, bool $showEditButton, string $actionValue = "", int $messageValue = 0) : void
    {
        $companyNameHTMLSafe = htmlspecialchars($companyData["CompanyName"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $ownerHTMLSafe = htmlspecialchars($companyData["Owner"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $streetHTMLSafe = htmlspecialchars($companyData["Street"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $zipCodeHTMLSafe = htmlspecialchars($companyData["ZipCode"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeHTMLSafe = htmlspecialchars($companyData["Place"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $emailHTMLSafe = htmlspecialchars($companyData["Email"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneMobileHTMLSafe = htmlspecialchars($companyData["PhoneMobile"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneNormalHTMLSafe = htmlspecialchars($companyData["PhoneNormal"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $p19ustgApplies = $companyData["P19UStGApplies"][0];
        $p36vsbgApplies = $companyData["P36VSBGApplies"][0];

        if(empty($actionValue))
        {
            $optionalEditImprintText = $this->globalView->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Impressum bearbeiten", "/?view=imprint&action=update");
            $optionalEditButton = $showEditButton ? "<div class=\"row mb-4\"><div class=\"col-12\">$optionalEditImprintText</div></div>" : "";

            $optionalP19UStGText = $this->globalView->genSpanText($appearanceData, "Per §19 Abs. 1 UStG wird die Umsatzsteuer nicht ausgewiesen.");
            $optionalP19UStGTextRow = ($p19ustgApplies === 1) ? "<div class=\"row mb-3\"><div class=\"col-12\">$optionalP19UStGText</div></div>" : "";

            $optionalP36VSBGTextHint = $this->globalView->genSpanText($appearanceData, "Hinweis zur Streitbeilegung gemäß § 36 VSBG:");
            $optionalP36VSBGTextBriefly = $this->globalView->genSpanText($appearanceData, "Wir nehmen nicht an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teil und sind hierzu auch nicht verpflichtet.");
            $optionalP36VSBGTextRow = ($p36vsbgApplies === 1) ? "<div class=\"row mb-3\"><div class=\"col-12\">$optionalP36VSBGTextHint</div></div><div class=\"row mb-3\"><div class=\"col-12\">$optionalP36VSBGTextBriefly</div></div>" : "";

            $imprintTitle = $this->globalView->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "fw-bold", "fs-3" ], "Impressum");
            $imprintCompanyName = $this->globalView->genSpanText($appearanceData, $companyNameHTMLSafe);
            $imprintStreet = $this->globalView->genSpanText($appearanceData, $streetHTMLSafe);
            $imprintZipCode = $this->globalView->genSpanText($appearanceData, $zipCodeHTMLSafe);
            $imprintPlace = $this->globalView->genSpanText($appearanceData, $placeHTMLSafe);

            $imprintEmailTitle = $this->globalView->genSpanText($appearanceData, "Email:&nbsp;");
            $imprintEmail = $this->globalView->genALink($appearanceData, $emailHTMLSafe, "mailto:$emailHTMLSafe");

            $imprintTelTitle = $this->globalView->genSpanText($appearanceData, "Telefon:&nbsp;");
            $imprintTel = $this->globalView->genALink($appearanceData, $phoneMobileHTMLSafe, "mailto:$phoneMobileHTMLSafe");

            $imprintOwnerTitle = $this->globalView->genSpanText($appearanceData, "Geschäftsführer:&nbsp;");
            $imprintOwner = $this->globalView->genSpanText($appearanceData, $ownerHTMLSafe);

            $imprintTermsOfServiceTitle = $this->globalView->genSpanText($appearanceData, "Allgemeine Geschäftsbedingungen:");
            $imprintTermsOfService = $this->globalView->genALink($appearanceData, "/?view=termsofservice", "/?view=termsofservice");

            $imprintLandingPageButton = $this->globalView->genAButtonInteresting($appearanceData, "Zur Startseite", "/");

            $imprintContentHTML =
            "
            <div class=\"container-fluid p-0\">
                <div class=\"row lh-1\">
                    <div class=\"col-12\">
                        $imprintTitle
                    </div>
                </div>
                $optionalEditButton
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $imprintCompanyName
                    </div>
                </div>
                <div class=\"row\">
                    <div class=\"col-12\">
                        $imprintStreet
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $imprintZipCode&nbsp;$imprintPlace
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $imprintEmailTitle$imprintEmail
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $imprintTelTitle$imprintTel
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $imprintOwnerTitle$imprintOwner
                    </div>
                </div>
                <div class=\"row\">
                    <div class=\"col-12\">
                        $imprintTermsOfServiceTitle
                    </div>
                </div>
                <div class=\"row mb-4\">
                    <div class=\"col-12\">
                        $imprintTermsOfService
                    </div>
                </div>
                $optionalP36VSBGTextRow
                $optionalP19UStGTextRow
                <div class=\"row\">
                    <div class=\"col-12\">
                        $imprintLandingPageButton
                    </div>
                </div>
            </div>
            ";
        }
        else if(strcmp($actionValue, "update") === 0)
        {
            if($messageValue <= 0) // Show the form and highlight the invalid field, based on $messageValue given.
            {
                $companyNameMaxLength = $companyData["CompanyName"][1];
                $companyNameLabelBSClass = "";
                $companyNameInputBSClass = "";
                if($messageValue === -1)
                {
                    $companyNameLabelBSClass = " text-danger";
                    $companyNameInputBSClass = " is-invalid";
                }

                $streetMaxLength = $companyData["Street"][1];
                $streetLabelBSClass = "";
                $streetInputBSClass = "";
                if($messageValue === -2)
                {
                    $streetLabelBSClass = " text-danger";
                    $streetInputBSClass = " is-invalid";
                }

                $zipCodeMaxLength = $companyData["ZipCode"][1];
                $zipCodeLabelBSClass = "";
                $zipCodeInputBSClass = "";
                if($messageValue === -3)
                {
                    $zipCodeLabelBSClass = " text-danger";
                    $zipCodeInputBSClass = " is-invalid";
                }

                $placeMaxLength = $companyData["Place"][1];
                $placeLabelBSClass = "";
                $placeInputBSClass = "";
                if($messageValue === -4)
                {
                    $placeLabelBSClass = " text-danger";
                    $placeInputBSClass = " is-invalid";
                }

                $emailMaxLength = $companyData["Email"][1];
                $emailLabelBSClass = "";
                $emailInputBSClass = "";
                if($messageValue === -5)
                {
                    $emailLabelBSClass = " text-danger";
                    $emailInputBSClass = " is-invalid";
                }

                $phoneMobileMaxLength = $companyData["PhoneMobile"][1];
                $phoneMobileLabelBSClass = "";
                $phoneMobileInputBSClass = "";
                if($messageValue === -6)
                {
                    $phoneMobileLabelBSClass = " text-danger";
                    $phoneMobileInputBSClass = " is-invalid";
                }

                $phoneNormalMaxLength = $companyData["PhoneNormal"][1];
                $phoneNormalLabelBSClass = "";
                $phoneNormalInputBSClass = "";
                if($messageValue === -7)
                {
                    $phoneNormalLabelBSClass = " text-danger";
                    $phoneNormalInputBSClass = " is-invalid";
                }

                $ownerMaxLength = $companyData["Owner"][1];
                $ownerLabelBSClass = "";
                $ownerInputBSClass = "";
                if($messageValue === -8)
                {
                    $ownerLabelBSClass = " text-danger";
                    $ownerInputBSClass = " is-invalid";
                }

                $p19UStGAppliesHTML = ($p19ustgApplies === 1) ? " checked" : "";
                $p19ustgAppliesLabelBSClass = "";
                $p19ustgAppliesInputBSClass = "";
                if($messageValue === -9)
                {
                    $p19ustgAppliesLabelBSClass = " text-danger";
                    $p19ustgAppliesInputBSClass = " is-invalid";
                }

                $p36VSBGAppliesHTML = ($p36vsbgApplies === 1) ? " checked" : "";
                $p36vsbgAppliesLabelBSClass = "";
                $p36vsbgAppliesInputBSClass = "";
                if($messageValue === -10)
                {
                    $p36vsbgAppliesLabelBSClass = " text-danger";
                    $p36vsbgAppliesInputBSClass = " is-invalid";
                }

                $imprintContentHTML =
                "
                    <div class=\"container-fluid\">
                        <div class=\"row\">
                            <div class=\"col-12\">
                                <p class=\"fs-1\">Impressum aktualisieren</p>
                            </div>
                        </div>
                        <form class=\"row form\" action=\"/?view=imprint&action=update\" method=\"post\">
                            <div class=\"col-12\">
                                <div class=\"container-fluid p-0\">
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$companyNameLabelBSClass\" for=\"companyName\">Unternehmensname (max. $companyNameMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control$companyNameInputBSClass\" id=\"companyName\" name=\"companyName\" value=\"{$companyNameHTMLSafe}\" maxlength=\"$companyNameMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$streetLabelBSClass\" for=\"street\">Straße (inkl. Nr., max. $streetMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control$streetInputBSClass\" id=\"street\" name=\"street\" value=\"{$streetHTMLSafe}\" maxlength=\"$streetMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$zipCodeLabelBSClass\" for=\"zipCode\">PLZ (max. $zipCodeMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control$zipCodeInputBSClass\" id=\"zipCode\" name=\"zipCode\" value=\"{$zipCodeHTMLSafe}\" maxlength=\"$zipCodeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$placeLabelBSClass\" for=\"place\">Ort (max. $placeMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control$placeInputBSClass\" id=\"place\" name=\"place\" value=\"{$placeHTMLSafe}\" maxlength=\"$placeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$emailLabelBSClass\" for=\"email\">E-Mail (max. $emailMaxLength Zeichen):</label>
                                            <input type=\"email\" class=\"w-100 form-control$emailInputBSClass\" id=\"email\" name=\"email\" value=\"{$emailHTMLSafe}\" maxlength=\"$emailMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$phoneMobileLabelBSClass\" for=\"phoneMobile\">Telefon (mobil, max. $phoneMobileMaxLength Zeichen):</label>
                                            <input type=\"tel\" class=\"w-100 form-control$phoneMobileInputBSClass\" id=\"phoneMobile\" name=\"phoneMobile\" value=\"{$phoneMobileHTMLSafe}\" maxlength=\"$phoneMobileMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$phoneNormalLabelBSClass\" for=\"phoneNormal\">Telefon (normal, max. $phoneNormalMaxLength Zeichen):</label>
                                            <input type=\"tel\" class=\"w-100 form-control$phoneNormalInputBSClass\" id=\"phoneNormal\" name=\"phoneNormal\" value=\"{$phoneNormalHTMLSafe}\" maxlength=\"$phoneNormalMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label$ownerLabelBSClass\" for=\"owner\">Geschäftsführer (Vor- & Zuname, max. $ownerMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control$ownerInputBSClass\" id=\"owner\" name=\"owner\" value=\"{$ownerHTMLSafe}\" maxlength=\"$ownerMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <hr class=\"w-100 border border-1 border-primary\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12 align-items-start\">
                                            <label class=\"form-label$p19ustgAppliesLabelBSClass\" for=\"p19UStGApplies\">Hinweis zu §19 Abs. 1 UStG anzeigen:</label>
                                            <input type=\"checkbox\" class=\"form-check$p19ustgAppliesInputBSClass\" id=\"p19UStGApplies\" name=\"p19UStGApplies\"{$p19UStGAppliesHTML}>
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12 align-items-start\">
                                            <label class=\"form-label$p36vsbgAppliesLabelBSClass\" for=\"p36VSBGApplies\">Hinweis zur Streitbeilegung gemäß § 36 VSBG anzeigen:</label>
                                            <input type=\"checkbox\" class=\"form-check$p36vsbgAppliesInputBSClass\" id=\"p36VSBGApplies\" name=\"p36VSBGApplies\"{$p36VSBGAppliesHTML}>
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <hr class=\"w-100 border border-1 border-primary\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <input type=\"submit\" class=\"w-100 btn btn-primary\" value=\"Speichern\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <a class=\"w-100 btn btn-secondary\" href=\"/?view=imprint\">Zurück</a>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </form>   
                    </div>
                ";
            }
            else if($messageValue === 1) // Show that update was all ok.
            {
                $imprintContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-2\" style=\"background-image: url('/img/symbol/trash-fill-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                        <div class=\"col-10\">
                           <span class=\"fs-2 fw-bold text-success\">Impressum erfolgreich aktualisiert!</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-success w-100\" href=\"/?view=imprint&action=update\">Impressum weiter bearbeiten</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=imprint\">Zur Übersicht</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else
            {
                // Invalid request
            }
        }

        $feedHTML = $this->globalView->genFeed($appearanceData, null, $imprintContentHTML);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}