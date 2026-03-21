<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IImprintView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class ImprintView implements IImprintView
{
    private IHtmlFactory $htmlFactory;
    private IComponentFactory $componentFactory;

    private IGlobalView $globalView;

    public function __construct(IHtmlFactory $htmlFactory, IComponentFactory $componentFactory, IGlobalView $globalView)
    {
        $this->htmlFactory = $htmlFactory;
        $this->componentFactory = $componentFactory;

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
            $hr = $this->htmlFactory->genHr($appearanceData);

            $optionalEditImprintText = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Bearbeiten", "/?view=imprint&action=update");
            $optionalEditButton = $showEditButton ?
            "
            <div class=\"row mb-1\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            <div class=\"row mb-1\">
                <div class=\"col-12\">
                    $optionalEditImprintText
                </div>
            </div>
            <div class=\"row mb-1\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            " : "";

            $optionalP19UStGText = $this->htmlFactory->genSpanText($appearanceData, "Per §19 Abs. 1 UStG wird die Umsatzsteuer nicht ausgewiesen.");
            $optionalP19UStGTextRow = ($p19ustgApplies === 1) ? "<div class=\"row mb-3\"><div class=\"col-12\">$optionalP19UStGText</div></div>" : "";

            $optionalP36VSBGTextHint = $this->htmlFactory->genSpanText($appearanceData, "Hinweis zur Streitbeilegung gemäß § 36 VSBG:");
            $optionalP36VSBGTextBriefly = $this->htmlFactory->genSpanText($appearanceData, "Wir nehmen nicht an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teil und sind hierzu auch nicht verpflichtet.");
            $optionalP36VSBGTextRow = ($p36vsbgApplies === 1) ? "<div class=\"row mb-3\"><div class=\"col-12\">$optionalP36VSBGTextHint</div></div><div class=\"row mb-3\"><div class=\"col-12\">$optionalP36VSBGTextBriefly</div></div>" : "";

            $imprintTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fw-bold", "fs-3", "lh-1" ], "Impressum");
            $imprintCompanyName = $this->htmlFactory->genSpanText($appearanceData, $companyNameHTMLSafe);
            $imprintStreet = $this->htmlFactory->genSpanText($appearanceData, $streetHTMLSafe);
            $imprintZipCode = $this->htmlFactory->genSpanText($appearanceData, $zipCodeHTMLSafe);
            $imprintPlace = $this->htmlFactory->genSpanText($appearanceData, $placeHTMLSafe);

            $imprintEmailTitle = $this->htmlFactory->genSpanText($appearanceData, "Email:&nbsp;");
            $imprintEmail = $this->htmlFactory->genALink($appearanceData, $emailHTMLSafe, "mailto:$emailHTMLSafe");

            $imprintTelTitle = $this->htmlFactory->genSpanText($appearanceData, "Telefon:&nbsp;");
            $imprintTel = $this->htmlFactory->genALink($appearanceData, $phoneMobileHTMLSafe, "mailto:$phoneMobileHTMLSafe");

            $imprintOwnerTitle = $this->htmlFactory->genSpanText($appearanceData, "Geschäftsführer:&nbsp;");
            $imprintOwner = $this->htmlFactory->genSpanText($appearanceData, $ownerHTMLSafe);

            $imprintTermsOfServiceTitle = $this->htmlFactory->genSpanText($appearanceData, "Allgemeine Geschäftsbedingungen:");
            $imprintTermsOfService = $this->htmlFactory->genALink($appearanceData, "/?view=termsofservice", "/?view=termsofservice");

            $imprintLandingPageButton = $this->htmlFactory->genAButtonInteresting($appearanceData, "Zur Startseite", "/");

            $titleMarginBottom = $showEditButton ? "mb-1" : "mb-4";

            $imprintContentHTML =
            "
            <div class=\"container-fluid p-0\">
                <div class=\"row lh-1 $titleMarginBottom\">
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
                <div class=\"row mt-4\">
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
                $companyNameInputBSClass = "";
                if($messageValue === -1)
                {
                    $companyNameInputBSClass = " is-invalid";
                }

                $streetMaxLength = $companyData["Street"][1];
                $streetInputBSClass = "";
                if($messageValue === -2)
                {
                    $streetInputBSClass = " is-invalid";
                }

                $zipCodeMaxLength = $companyData["ZipCode"][1];
                $zipCodeInputBSClass = "";
                if($messageValue === -3)
                {
                    $zipCodeInputBSClass = " is-invalid";
                }

                $placeMaxLength = $companyData["Place"][1];
                $placeInputBSClass = "";
                if($messageValue === -4)
                {
                    $placeInputBSClass = " is-invalid";
                }

                $emailMaxLength = $companyData["Email"][1];
                $emailInputBSClass = "";
                if($messageValue === -5)
                {
                    $emailInputBSClass = " is-invalid";
                }

                $phoneMobileMaxLength = $companyData["PhoneMobile"][1];
                $phoneMobileInputBSClass = "";
                if($messageValue === -6)
                {
                    $phoneMobileInputBSClass = " is-invalid";
                }

                $phoneNormalMaxLength = $companyData["PhoneNormal"][1];
                $phoneNormalInputBSClass = "";
                if($messageValue === -7)
                {
                    $phoneNormalInputBSClass = " is-invalid";
                }

                $ownerMaxLength = $companyData["Owner"][1];
                $ownerInputBSClass = "";
                if($messageValue === -8)
                {
                    $ownerInputBSClass = " is-invalid";
                }

                $p19UStGAppliesHTML = ($p19ustgApplies === 1) ? " checked" : "";
                $p19ustgAppliesInputBSClass = "";
                if($messageValue === -9)
                {
                    $p19ustgAppliesInputBSClass = " is-invalid";
                }

                $p36VSBGAppliesHTML = ($p36vsbgApplies === 1) ? " checked" : "";
                $p36vsbgAppliesInputBSClass = "";
                if($messageValue === -10)
                {
                    $p36vsbgAppliesInputBSClass = " is-invalid";
                }

                $spanImprintTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-3", "fw-bold", "lh-1" ], "Impressum aktualisieren");

                $labelCompanyName = $this->htmlFactory->genLabelText($appearanceData, "Unternehmensname (max. $companyNameMaxLength Zeichen):", "companyName");
                $labelStreet = $this->htmlFactory->genLabelText($appearanceData, "Straße (inkl. Nr., max. $streetMaxLength Zeichen):", "street");
                $labelZipCode = $this->htmlFactory->genLabelText($appearanceData, "PLZ (max. $zipCodeMaxLength Zeichen):", "zipCode");
                $labelPlace = $this->htmlFactory->genLabelText($appearanceData, "Ort (max. $placeMaxLength Zeichen):", "place");

                $labelEmail = $this->htmlFactory->genLabelText($appearanceData, "E-Mail (max. $emailMaxLength Zeichen):", "email");

                $labelPhoneMobile = $this->htmlFactory->genLabelText($appearanceData, "Telefon (mobil, max. $phoneMobileMaxLength Zeichen):", "phoneMobile");
                $labelPhoneNormal = $this->htmlFactory->genLabelText($appearanceData, "Telefon (normal, max. $phoneNormalMaxLength Zeichen):", "phoneNormal");

                $labelOwner = $this->htmlFactory->genLabelText($appearanceData, "Geschäftsführer (Vor- & Zuname, max. $ownerMaxLength Zeichen):", "owner");

                $labelP19UStGApplies = $this->htmlFactory->genLabelText($appearanceData, "Hinweis zu §19 Abs. 1 UStG anzeigen:", "p19UStGApplies");
                $labelP36VSBGApplies = $this->htmlFactory->genLabelText($appearanceData, "Hinweis zur Streitbeilegung gemäß § 36 VSBG anzeigen:", "p36VSBGApplies");

                $hr = $this->htmlFactory->genHr($appearanceData);

                // <input type=\"submit\" class=\"w-100 btn btn-primary\" value=\"Speichern\">
                $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "w-100", "btn" ], "", "type=\"submit\" value=\"Speichern\"");

                // <a class=\"w-100 btn btn-secondary\" href=\"/?view=imprint\">Zurück</a>
                $aReturnButton = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Zurück", "/?view=imprint", [ "w-100", "btn" ]);

                $imprintContentHTML =
                "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-1\">
                            <div class=\"col-12 text-center\">
                                $spanImprintTitle
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12\">
                                $hr
                            </div>
                        </div>
                        <form class=\"row form\" action=\"/?view=imprint&action=update\" method=\"post\">
                            <div class=\"col-12\">
                                <div class=\"container-fluid p-0\">
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelCompanyName
                                            <input type=\"text\" class=\"w-100 form-control$companyNameInputBSClass\" id=\"companyName\" name=\"companyName\" value=\"{$companyNameHTMLSafe}\" maxlength=\"$companyNameMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelStreet
                                            <input type=\"text\" class=\"w-100 form-control$streetInputBSClass\" id=\"street\" name=\"street\" value=\"{$streetHTMLSafe}\" maxlength=\"$streetMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelZipCode
                                            <input type=\"text\" class=\"w-100 form-control$zipCodeInputBSClass\" id=\"zipCode\" name=\"zipCode\" value=\"{$zipCodeHTMLSafe}\" maxlength=\"$zipCodeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelPlace
                                            <input type=\"text\" class=\"w-100 form-control$placeInputBSClass\" id=\"place\" name=\"place\" value=\"{$placeHTMLSafe}\" maxlength=\"$placeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelEmail
                                            <input type=\"email\" class=\"w-100 form-control$emailInputBSClass\" id=\"email\" name=\"email\" value=\"{$emailHTMLSafe}\" maxlength=\"$emailMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelPhoneMobile
                                            <input type=\"tel\" class=\"w-100 form-control$phoneMobileInputBSClass\" id=\"phoneMobile\" name=\"phoneMobile\" value=\"{$phoneMobileHTMLSafe}\" maxlength=\"$phoneMobileMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelPhoneNormal
                                            <input type=\"tel\" class=\"w-100 form-control$phoneNormalInputBSClass\" id=\"phoneNormal\" name=\"phoneNormal\" value=\"{$phoneNormalHTMLSafe}\" maxlength=\"$phoneNormalMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $labelOwner
                                            <input type=\"text\" class=\"w-100 form-control$ownerInputBSClass\" id=\"owner\" name=\"owner\" value=\"{$ownerHTMLSafe}\" maxlength=\"$ownerMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-2\">
                                        <div class=\"col-12\">
                                            $hr
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12 align-items-start\">
                                            $labelP19UStGApplies
                                            <input type=\"checkbox\" class=\"form-check$p19ustgAppliesInputBSClass\" id=\"p19UStGApplies\" name=\"p19UStGApplies\"{$p19UStGAppliesHTML}>
                                        </div>
                                    </div>
                                    <div class=\"row mb-2\">
                                        <div class=\"col-12 align-items-start\">
                                            $labelP36VSBGApplies
                                            <input type=\"checkbox\" class=\"form-check$p36vsbgAppliesInputBSClass\" id=\"p36VSBGApplies\" name=\"p36VSBGApplies\"{$p36VSBGAppliesHTML}>
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $hr
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $inputSubmitButton
                                        </div>
                                    </div>
                                    <div class=\"row\">
                                        <div class=\"col-12\">
                                            $aReturnButton
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
                $imprintContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/shield-fill-check.svg", "/?view=imprint", "/?view=imprint&action=update", "Impressum erfolgreich aktualisiert!");
            }
            else
            {
                // Invalid request
            }
        }

        $feedHTML = $this->componentFactory->genFeed($appearanceData, null, $imprintContentHTML);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}