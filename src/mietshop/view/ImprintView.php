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

    public function show(array $companyData, string $actionValue = "", int $messageValue = 0) : void
    {
        $companyNameHTMLSafe = htmlspecialchars($companyData["CompanyName"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $ownerHTMLSafe = htmlspecialchars($companyData["Owner"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $streetHTMLSafe = htmlspecialchars($companyData["Street"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $zipCodeHTMLSafe = htmlspecialchars($companyData["ZipCode"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeHTMLSafe = htmlspecialchars($companyData["Place"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $emailHTMLSafe = htmlspecialchars($companyData["Email"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneMobileHTMLSafe = htmlspecialchars($companyData["PhoneMobile"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneNormalHTMLSafe = htmlspecialchars($companyData["PhoneNormal"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if(empty($actionValue)) // $messageValue === 0)
        {
            $imprintContentHTML =
            "
            <span class=\"fs-6\" style=\"white-space: pre-line;\">
            <p class=\"fs-1\">Impressum</p>
            {$companyNameHTMLSafe}
    
            {$streetHTMLSafe}
            {$zipCodeHTMLSafe} {$placeHTMLSafe}
            E-Mail: <a href=\"mailto:{$emailHTMLSafe}\">{$emailHTMLSafe}</a>
            Telefon: <a href=\"tel:{$phoneMobileHTMLSafe}\">{$phoneMobileHTMLSafe}</a>
    
            Geschäftsführer: {$ownerHTMLSafe}
    
            Allgemeine Geschäftsbedingungen:
            <a href=\"/?view=termsofservice\">/termsofservice</a>
    
            Hinweis zur Streitbeilegung gemäß § 36 VSBG:
            Wir nehmen nicht an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teil und sind hierzu auch nicht verpflichtet.
    
            Per §19 Abs. 1 UStG wird die Umsatzsteuer nicht ausgewiesen.
    
            <a class=\"btn btn-primary mb-3\" href=\"/\">Zur Startseite</a>
            ";
        }
        else if(strcmp($actionValue, "update") === 0)
        {
            if($messageValue <= 0) // Show the form and highlight the invalid field, based on $messageValue given.
            {
                $p19ustgValue = $companyData["P19UStG"][0];
                $p36vsbgValue = $companyData["P36VSBG"][0];

                $p19UStGIsChecked = ($p19ustgValue == 1) ? " checked" : "";
                $p36VSBGIsChecked = ($p36vsbgValue == 1) ? " checked" : "";

                $companyNameMaxLength = $companyData["CompanyName"][1];
                $ownerMaxLength = $companyData["Owner"][1];
                $streetMaxLength = $companyData["Street"][1];
                $zipCodeMaxLength = $companyData["ZipCode"][1];
                $placeMaxLength = $companyData["Place"][1];
                $emailMaxLength = $companyData["Email"][1];
                $phoneMobileMaxLength = $companyData["PhoneMobile"][1];
                $phoneNormalMaxLength = $companyData["PhoneNormal"][1];

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
                                            <label class=\"form-label\" for=\"companyName\">Unternehmensname (max. $companyNameMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control\" id=\"companyName\" name=\"companyName\" value=\"{$companyNameHTMLSafe}\" maxlength=\"$companyNameMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"street\">Straße (inkl. Nr., max. $streetMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control\" id=\"street\" name=\"street\" value=\"{$streetHTMLSafe}\" maxlength=\"$streetMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"zipCode\">PLZ (max. $zipCodeMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control\" id=\"zipCode\" name=\"zipCode\" value=\"{$zipCodeHTMLSafe}\" maxlength=\"$zipCodeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"place\">Ort (max. $placeMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control\" id=\"place\" name=\"place\" value=\"{$placeHTMLSafe}\" maxlength=\"$placeMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"email\">E-Mail (max. $emailMaxLength Zeichen):</label>
                                            <input type=\"email\" class=\"w-100 form-control\" id=\"email\" name=\"email\" value=\"{$emailHTMLSafe}\" maxlength=\"$emailMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"phoneMobile\">Telefon (mobil, max. $phoneMobileMaxLength Zeichen):</label>
                                            <input type=\"tel\" class=\"w-100 form-control\" id=\"phoneMobile\" name=\"phoneMobile\" value=\"{$phoneMobileHTMLSafe}\" maxlength=\"$phoneMobileMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"phoneNormal\">Telefon (normal, max. $phoneNormalMaxLength Zeichen):</label>
                                            <input type=\"tel\" class=\"w-100 form-control\" id=\"phoneNormal\" name=\"phoneNormal\" value=\"{$phoneNormalHTMLSafe}\" maxlength=\"$phoneNormalMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <label class=\"form-label\" for=\"owner\">Geschäftsführer (Vor- & Zuname, max. $ownerMaxLength Zeichen):</label>
                                            <input type=\"text\" class=\"w-100 form-control\" id=\"owner\" name=\"owner\" value=\"{$ownerHTMLSafe}\" maxlength=\"$ownerMaxLength\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <hr class=\"w-100 border border-1 border-primary\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12 align-items-start\">
                                            <label class=\"form-label\" for=\"p19UStG\">Hinweis zu §19 Abs. 1 UStG anzeigen:</label>
                                            <input type=\"checkbox\" class=\"form-check\" id=\"p19UStG\" name=\"p19UStG\"{$p19UStGIsChecked}>
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12 align-items-start\">
                                            <label class=\"form-label\" for=\"p36VSBG\">Hinweis zur Streitbeilegung gemäß § 36 VSBG anzeigen:</label>
                                            <input type=\"checkbox\" class=\"form-check\" id=\"p36VSBG\" name=\"p36VSBG\"{$p36VSBGIsChecked}>
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
                                            <a class=\"w-100 btn btn-secondary\" href=\"/\">Zur Startseite</a>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </form>   
                    </div>
                ";

                /**
                    Hinweis zur Streitbeilegung gemäß § 36 VSBG:
                    Wir nehmen nicht an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teil und sind hierzu auch nicht verpflichtet.

                    Per §19 Abs. 1 UStG wird die Umsatzsteuer nicht ausgewiesen.
                */
            }
            else if($messageValue === 1) // Show that update was all ok.
            {
                $imprintContentHTML =
                "
                    
                ";
            }
        }

        $feedHTML = $this->globalView->genFeed(null, $imprintContentHTML);

        $body = "<div class=\"container-fluid\">$feedHTML</div>";

        $this->globalView->show($companyData, $body);
    }
}