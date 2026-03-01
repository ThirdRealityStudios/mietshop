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

    public function show(array $companyData, string $errorFeedTitle, string $errorFeedContent) : void
    {
        $errorFeedTitleSafe = htmlspecialchars($errorFeedTitle, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        $errorFeedContentSafe = htmlspecialchars($errorFeedContent, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $feedDescriptionHTML =
        "
        <div class=\"container-fluid\">
            <div class=\"row mb-3\">
                <div class=\"col-12\">
                    <p class=\"text-center fs-2 fw-bold text-danger\">$errorFeedTitleSafe</p>
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    <p class=\"text-left fs-6\">$errorFeedContentSafe</p>
                </div>
            </div>
        </div>
        ";

        $body = "<div class=\"container-fluid\">".$this->globalView->genFeed(null, $feedDescriptionHTML)."</div>";

        $this->globalView->show($companyData, $body);
    }
}