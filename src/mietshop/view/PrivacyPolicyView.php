<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IPrivacyPolicyView;

class PrivacyPolicyView implements IPrivacyPolicyView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData)
    {
        if(isset($companyData["PrivacyPolicy"][0]) && is_string($companyData["PrivacyPolicy"][0]))
        {
            $termsOfServiceText = $companyData["PrivacyPolicy"][0];
        }
        else
        {
            $termsOfServiceText = "Datenschutzerklärung ist aus technischen Gründen derzeit nicht abrufbar. Bitte versuchen Sie es später erneut. Wir arbeiten so schnellst wie möglich an einer Lösung.";
        }

        $privacyPolicyTextHTMLSafe = htmlspecialchars($termsOfServiceText, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $feed =
            "
        <span class=\"fs-6 text-center\" style=\"white-space: pre-line;\">
            <p class=\"fs-1\">Datenschutzerklärung</p>
        </span>
        <p class=\"fs-6 mb-4\">$privacyPolicyTextHTMLSafe</p>
        <a class=\"btn btn-primary mb-3\" href=\"/\">Zur Startseite</a>
        ";

        $feedHTML = $this->globalView->genFeed(null, $feed);

        $this->globalView->show($companyData, "<div class=\"container-fluid\">$feedHTML</div>");

        $feedContentHTML =
        "
            bullshit
        ";

        $body = $this->globalView->genFeed(null, $feedContentHTML);

        $this->globalView->show($companyData, $body);
    }
}