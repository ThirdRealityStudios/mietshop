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
        if(isset($companyData["TermsOfService"][0]) && is_string($companyData["TermsOfService"][0]))
        {
            $termsOfServiceText = $companyData["TermsOfService"][0];
        }
        else
        {
            $termsOfServiceText = "Derzeit gibt es keine Allgemeinen Geschäfsbedingungen. Entweder ist dies ein technischer Fehler oder es wurden noch keine AGBs festgelegt.";
        }

        $termsOfServiceTextHTMLSafe = htmlspecialchars($termsOfServiceText, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $feed =
        "
        <span class=\"fs-6 text-center\" style=\"white-space: pre-line;\">
            <p class=\"fs-1\">Allgemeine Geschäftsbedingungen</p>
        </span>
        <p class=\"fs-6 mb-4\">$termsOfServiceTextHTMLSafe</p>
        <a class=\"btn btn-primary mb-3\" href=\"/\">Zur Startseite</a>
        ";

        $feedHTML = $this->globalView->genFeed(null, $feed);

        $this->globalView->show($companyData, "<div class=\"container-fluid\">$feedHTML</div>");
    }
}