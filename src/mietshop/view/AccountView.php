<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\IGlobalView;

class AccountView implements IAccountView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    private function genAccountMenuButton(string $view, string $action, string $title) : string
    {
        $menuButtonHTML =
        "
        <div class=\"row mt-4\">
            <div class=\"col-12\">
                <a class=\"btn btn-primary w-100\" href=\"/?view=$view&action=$action\">$title</a>
            </div>
        </div>
        ";

        // $menuButtonHTML = $this->globalView->genAButtonInteresting($appearanceData, $title, "/?view=$view&action=$action");

        return $menuButtonHTML;
    }

    public function show(array $companyData, array $appearanceData, string $email, bool $isSupervisor, string $permissionRoleName) : void
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

        $logoffButton = $this->globalView->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Abmelden 🔐", "/?view=account&action=logout");

        if($isSupervisor)
        {
            $optionalManageAccountsButton = $this->globalView->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Konten verwalten 👥", "/?view=accounts&action=all");
            $optionalManageAppearanceButton = $this->globalView->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Darstellung verwalten 🖼️", "/?view=appearance&action=all");
        }
        else
        {
            $optionalManageAccountsButton = "";
            $optionalManageAppearanceButton = "";
        }

        $feedContent =
        "
            <div class=\"container-fluid p-0\">
                <div class=\"row mb-5 ms-0 mt-0 me-0\">
                    <div class=\"col-2 p-0\" style=\"background-image: url('/img/symbol/person-square.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                    <div class=\"col-10 p-0\">
                        <div class=\"container-fluid\">
                            <div class=\"row\">
                                <div class=\"col-12 text-left\">
                                    <span class=\"fs-6 fw-bold\">{$accountEmailSafe}</span>
                                </div>
                            </div>
                            <div class=\"row\">
                                <div class=\"col-12 text-left\">
                                    <span>Sie sind: {$permissionRoleName}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        $feedHTML = $this->globalView->genFeed($appearanceData, null, $feedContent);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}