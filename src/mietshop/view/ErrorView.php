<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class ErrorView implements IErrorView
{
    private IComponentFactory $componentFactory;
    private IHtmlFactory $htmlFactory;

    private IGlobalView $globalView;

    public function __construct(IHtmlFactory $htmlFactory, IComponentFactory $componentFactory, IGlobalView $globalView)
    {
        $this->htmlFactory = $htmlFactory;
        $this->componentFactory = $componentFactory;

        $this->globalView = $globalView;
    }

    public function show(array $companyData, array $appearanceData, string $errorFeedTitle, string $errorFeedContent = "") : void
    {
        $errorFeedTitleSafe = htmlspecialchars($errorFeedTitle, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        $errorFeedContentSafe = htmlspecialchars($errorFeedContent, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $pErrorTitle = $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "text-center", "fs-2", "fw-bold", "lh-1" ], $errorFeedTitleSafe);
        $pErrorDescription = $this->htmlFactory->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "text-left", "fs-6", "m-0" ], $errorFeedContentSafe);

        $feedDescriptionHTML =
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row ms-0 mt-0 me-0 mb-3\">
                <div class=\"col-12\">
                    $pErrorTitle
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    $pErrorDescription
                </div>
            </div>
        </div>
        ";

        $body = $this->componentFactory->genFeed($appearanceData, null, $feedDescriptionHTML);

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}