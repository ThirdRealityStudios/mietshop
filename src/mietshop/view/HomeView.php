<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IHomeView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class HomeView implements IHomeView
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

    public function show(array $companyData, array $appearanceData, array $categories): void
    {
        $body = "";

        $ourRentalObjectsHTMLContent = "";

        $pOurRentalObjects = $this->htmlFactory->genNonSelfClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-4", "mb-3", "lh-1" ], "Unser Verleihsortiment");
        $pNumerousObjectsHint = $this->htmlFactory->genNonSelfClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-6", "mb-3" ], "Zahlreiche Mietartikel aus folgenden Kategorien bieten wir an 😊");

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

                $ourRentalObjectsHTMLContent .= $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "mb-3" ], $safeCategoryName);
            }

            $ourRentalObjectsHTMLContent .= $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn" ], "Jetzt stöbern und anfragen", "/?view=search");
        }
        else
        {
            // TODO Fetch amount of articles in the system.
            $articleAmount = 3;

            $spanRentalObjectsNotAvailableHint = $this->htmlFactory->genNonSelfClosingElement("span",[ "Text" ], $appearanceData, [], [ "fs-4" ], "Mietartikel im Aufbau&nbsp;");
            $pSorry = $this->htmlFactory->genPText($appearanceData, "Sorry, aber derzeit scheint der Inhaber noch keine Mietartikel oder Kategorien online gestellt zu haben.");
            $pComeBackLater = $this->htmlFactory->genPText($appearanceData, "Bitte schauen Sie später noch einmal vorbei.");
            $pObjectForecast = $this->htmlFactory->genPText($appearanceData, "Hier sollten bald zahlreiche Artikel erscheinen 😊");

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
                $pExploreHint = $this->htmlFactory->genNonSelfClosingElement("p",[ "Text" ], $appearanceData, [], [ "fs-6", "mb-4" ], "Klicken Sie auf die Schaltfläche, um unsere Mietartikel zu erkunden 😊");
                $aButtonExploreObjects = $this->htmlFactory->genAButtonInteresting($appearanceData, "Jetzt stöbern und anfragen", "/?view=search");

                $ourRentalObjectsHTMLContent =
                "
                $pOurRentalObjects
                $pExploreHint
                $aButtonExploreObjects
                ";
            }
        }

        $body .= $this->componentFactory->genFeed($appearanceData, null, $ourRentalObjectsHTMLContent);

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}