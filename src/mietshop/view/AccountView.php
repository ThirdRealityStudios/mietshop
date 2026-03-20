<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class AccountView implements IAccountView
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

    public function show(array $companyData, array $appearanceData, string $email, bool $isSupervisor, string $permissionRoleName, string $welcomeMessage) : void
    {
        $cutFromLength = 30;

        if(mb_strlen($email, "UTF-8") > $cutFromLength)
        {
            $accountEmailLimited = substr($email, 0, $cutFromLength);
            $accountEmailLimited .= "...";
        }
        else
        {
            $accountEmailLimited = $email;
        }

        $accountEmailSafe = htmlspecialchars($accountEmailLimited, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $logoffButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Abmelden 🔐", "/?view=account&action=logout");

        if($isSupervisor)
        {
            $optionalManageAccountsButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Konten verwalten 👥", "/?view=accounts&action=all");
            $optionalManageAppearanceButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Darstellung verwalten 🖼️", "/?view=appearance&action=all");
        }
        else
        {
            $optionalManageAccountsButton = "";
            $optionalManageAppearanceButton = "";
        }

        $personSquare = $this->htmlFactory->genTextSVG("/img/symbol/person-square.svg", "", [ "align-bottom" ]);

        $accountEmail = $this->htmlFactory->genNonSelfClosingElement("span", [], [], [], [ "fs-6", "fw-bold" ], $accountEmailSafe);
        $accountRole = $this->htmlFactory->genNonSelfClosingElement("span", [], [], [], [], "Sie sind: $permissionRoleName");

        $accountSummaryContent =
        "
        <div class=\"col-12 lh-1\">
            <span class=\"align-middle d-inline-block fs-3 fw-bold pe-5 mb-4\">
                $personSquare<span class=\"ms-4\">$welcomeMessage</span>
            </span>
            <span class=\"align-middle d-inline-block me-5 mb-3\">
                <div class=\"container-fluid p-0 m-0\">
                    <div class=\"row mb-2\">
                        <div class=\"col-12\">
                            $accountEmail
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-12\">
                            $accountRole
                        </div>
                    </div>
                </div>
            </span>
        </div>
        ";

        $accountSummary = $this->htmlFactory->genNonSelfClosingElement("div", [ "Text" ], $appearanceData, [], [ "row", "mb-4" ], $accountSummaryContent);

        $feedContent =
        "
            <div class=\"container-fluid p-0\">
                $accountSummary
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                        $logoffButton
                    </div>
                </div>
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                        $optionalManageAccountsButton
                    </div>
                </div>
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                        $optionalManageAppearanceButton
                    </div>
                </div>
            </div>
        ";

        $feedHTML = $this->componentFactory->genFeed($appearanceData, null, $feedContent);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}