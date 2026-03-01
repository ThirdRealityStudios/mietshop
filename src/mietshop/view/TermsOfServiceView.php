<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\ITermsOfServiceView;

class TermsOfServiceView implements ITermsOfServiceView
{
    private $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData): void
    {
        $termsOfServiceText = $companyData['TermsOfService'][0] ?? "";

        if(empty($termsOfServiceText))
        {
            $termsOfServiceText = "Es wurden noch keine Allgemeinen Geschäftsbedingungen eingestellt oder es liegt derzeit ein technischer Abfragefehler vor. Bitte versuchen Sie es später erneut.";
        }

        $termsOfServiceTextHTMLSafe = htmlspecialchars($termsOfServiceText, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $feed =
        "
        <div class=\"container-fluid\">
            <div class=\"row mb-3\">
                <div class=\"col-12\">
                    <p class=\"text-center fs-1\">Allgemeine Geschäftsbedingungen</p>
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    <p class=\"text-left fs-6\">$termsOfServiceTextHTMLSafe</p>
                </div>
            </div>
        </div>
        ";

        $feedHTML = $this->globalView->genFeed(null, $feed);

        $this->globalView->show($companyData, "<div class=\"container-fluid\">$feedHTML</div>");
    }
}