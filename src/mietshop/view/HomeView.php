<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IHomeView;

class HomeView implements IHomeView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(string $host, array $companyData, array $categories): void
    {
        $body = "";

        $ourRentalObjectsHTMLContent = "";

        if(sizeof($categories) > 2)
        {
            $ourRentalObjectsHTMLContent =
            "
            <p class=\"fs-4 mb-3\">Unser Verleihsortiment</p>
                <p class=\"fs-6 mb-3\">Zahlreiche Mietartikel aus folgenden Kategorien bieten wir an 😊</p>
                
            ";

            foreach($categories as $category)
            {
                $safeCategoryName = htmlspecialchars($category);

                $ourRentalObjectsHTMLContent .= "<p class=\"fs-6 mb-3\">• {$safeCategoryName}</p>";
            }

            $ourRentalObjectsHTMLContent .= "<a class=\"btn btn-primary mb-3\" href=\"/?view=search\">Jetzt stöbern und anfragen</a>";
        }
        else
        {
            // TODO Fetch amount of articles in the system.
            $articleAmount = 3;

            // Is there no article AND no category online? (then VERY likely website was freshly installed)
            if($articleAmount === 0)
            {
                $ourRentalObjectsHTMLContent .=
                "
                <div class=\"row\">
                    <div class=\"col-12\">
                        <span class=\"fs-4\">Mietartikel im Aufbau&nbsp;</span>
                        <img style=\"vertical-align: text-top;\" class=\"d-inline-block gear-spin\" width=\"17\" height=\"17\" src=\"/img/symbol/gear-fill.svg\" alt=\"gear-fill.svg\">
                    </div>
                </div>
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                        <p class=\"\">Sorry, aber derzeit scheint der Inhaber noch keine Mietartikel oder Kategorien online gestellt zu haben.</p>
                        <p class=\"\">Bitte schauen Sie später noch einmal vorbei.</p>
                        <p class=\"\">Hier sollten bald zahlreiche Artikel erscheinen 😊</p>
                    </div>
                </div>
                ";
            }
            else // No category was created for any article BUT at least one article is online (so give users the opportunity to go there and see)
            {
                $ourRentalObjectsHTMLContent =
                "
                <p class=\"fs-4 mb-3\">Unser Verleihsortiment</p>
                <p class=\"fs-6 mb-4\">Klicken Sie auf die Schaltfläche, um unsere Mietartikel zu erkunden 😊</p>
                <a class=\"btn btn-primary mb-3\" href=\"/?view=search\">Jetzt stöbern und anfragen</a>
                ";
            }
        }

        $body .= "<div class=\"container-fluid\">{$this->globalView->genFeed(null, $ourRentalObjectsHTMLContent)}</div>";

        $this->globalView->show($host, $companyData["CompanyName"][0], $companyData["Owner"][0], $companyData["Street"][0], $companyData["ZipCode"][0], $companyData["Place"][0], $companyData["Email"][0], $companyData["PhoneMobile"][0], $companyData["PhoneNormal"][0], $companyData["MapLocationURL"][0], $companyData["FacebookURL"][0], $companyData["InstagramURL"][0], $companyData["WhatsAppURL"][0], $companyData["KleinanzeigenURL"][0], $companyData["PrivacyPolicy"][0], $companyData["TermsOfService"][0], $body);
    }
}