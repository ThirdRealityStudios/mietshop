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

    public function show(array $companyData, array $appearanceData, array $categories): void
    {
        $body = "";

        $ourRentalObjectsHTMLContent = "";

        $pOurRentalObjects = $this->globalView->genNonClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-4", "mb-3", "lh-1" ], "Unser Verleihsortiment");
        $pNumerousObjectsHint = $this->globalView->genNonClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-6", "mb-3" ], "Zahlreiche Mietartikel aus folgenden Kategorien bieten wir an 😊");

        if(sizeof($categories) > 2)
        {
            $ourRentalObjectsHTMLContent =
            "
            $pOurRentalObjects
            $pNumerousObjectsHint
            ";

            foreach($categories as $category)
            {
                $safeCategoryName = htmlspecialchars($category);

                $ourRentalObjectsHTMLContent .= $this->globalView->genNonClosingElement("p", [ "Text" ], $appearanceData, [], [ "mb-3" ], $safeCategoryName);
            }

            $ourRentalObjectsHTMLContent .= $this->globalView->genNonClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn" ], "Jetzt stöbern und anfragen", "/?view=search");
        }
        else
        {
            // TODO Fetch amount of articles in the system.
            $articleAmount = 3;

            $spanRentalObjectsNotAvailableHint = $this->globalView->genNonClosingElement("span",[ "Text" ], $appearanceData, [], [ "fs-4" ], "Mietartikel im Aufbau&nbsp;");
            $pSorry = $this->globalView->genPText($appearanceData, "Sorry, aber derzeit scheint der Inhaber noch keine Mietartikel oder Kategorien online gestellt zu haben.");
            $pComeBackLater = $this->globalView->genPText($appearanceData, "Bitte schauen Sie später noch einmal vorbei.");
            $pObjectForecast = $this->globalView->genPText($appearanceData, "Hier sollten bald zahlreiche Artikel erscheinen 😊");

            // Is there no article AND no category online? (then VERY likely website was freshly installed)
            if($articleAmount === 0)
            {
                $ourRentalObjectsHTMLContent .=
                "
                <div class=\"row\">
                    <div class=\"col-12\">
                        $spanRentalObjectsNotAvailableHint
                        <img style=\"vertical-align: text-top;\" class=\"d-inline-block gear-spin\" width=\"17\" height=\"17\" src=\"/img/symbol/gear-fill.svg\" alt=\"gear-fill.svg\">
                    </div>
                </div>
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                        $pSorry
                        $pComeBackLater
                        $pObjectForecast
                    </div>
                </div>
                ";
            }
            else // No category was created for any article BUT at least one article is online (so give users the opportunity to go there and see)
            {
                $pExploreHint = $this->globalView->genNonClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-6", "mb-4" ], "Klicken Sie auf die Schaltfläche, um unsere Mietartikel zu erkunden 😊");
                $aButtonExploreObjects = $this->globalView->genAButtonInteresting($appearanceData, "Jetzt stöbern und anfragen", "/?view=search");

                $ourRentalObjectsHTMLContent =
                "
                $pOurRentalObjects
                $pExploreHint
                $aButtonExploreObjects
                ";
            }
        }

        $body .= $this->globalView->genFeed($appearanceData, null, $ourRentalObjectsHTMLContent);

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}