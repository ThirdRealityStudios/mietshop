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

    public function show(array $companyData) : void
    {
        $companyNameHTMLSafe = htmlspecialchars($companyData["CompanyName"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $ownerHTMLSafe = htmlspecialchars($companyData["Owner"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $streetHTMLSafe = htmlspecialchars($companyData["Street"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $zipCodeHTMLSafe = htmlspecialchars($companyData["ZipCode"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeHTMLSafe = htmlspecialchars($companyData["Place"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $emailHTMLSafe = htmlspecialchars($companyData["Email"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phoneMobileHTMLSafe = htmlspecialchars($companyData["PhoneMobile"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

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

        $feedHTML = $this->globalView->genFeed(null, $imprintContentHTML);

        $this->globalView->show($companyData, $feedHTML);
    }
}