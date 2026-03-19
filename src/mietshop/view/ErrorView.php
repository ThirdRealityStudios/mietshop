<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;

class ErrorView implements IErrorView
{
    private $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData, array $appearanceData, string $errorFeedTitle, string $errorFeedContent) : void
    {
        $errorFeedTitleSafe = htmlspecialchars($errorFeedTitle, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        $errorFeedContentSafe = htmlspecialchars($errorFeedContent, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $pErrorTitle = $this->globalView->genNonClosingElement("p", [ "Text" ], $appearanceData, [], [ "text-center", "fs-2", "fw-bold", "lh-1" ], $errorFeedTitleSafe);
        $pErrorDescription = $this->globalView->genNonClosingElement("p", [ "Text" ], $appearanceData, [], [ "text-left", "fs-6", "m-0" ], $errorFeedContentSafe);

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

        $body = $this->globalView->genFeed($appearanceData, null, $feedDescriptionHTML);

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}