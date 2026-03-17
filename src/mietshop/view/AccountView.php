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

        if($isSupervisor)
        {
            $optionalManageAccountsButton = $this->genAccountMenuButton("accounts", "all", "Konten verwalten 👥");
            $optionalManageAppearanceButton = $this->genAccountMenuButton("appearance", "all", "Darstellung verwalten 🖼️");
        }
        else
        {
            $optionalManageAccountsButton = "";
            $optionalManageAppearanceButton = "";
        }

        $feedContent =
        "
            <div class=\"container-fluid\">
                <div class=\"row mt-3 mb-5\">
                    <div class=\"col-2\" style=\"background-image: url('/img/symbol/person-square.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                    <div class=\"col-10\">
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
                <div class=\"row mt-4\">
                    <div class=\"col-12\">
                        <a class=\"btn btn-primary w-100\" href=\"/?view=account&action=logout\">Abmelden 🔐</a>
                    </div>
                </div>
                $optionalManageAccountsButton
                $optionalManageAppearanceButton
                <div class=\"row mt-3\">
                    <div class=\"col-12\">
                    </div>
                </div>
            </div>
        ";

        $feedHTML = $this->globalView->genFeed(null, $feedContent);

        $body = "<div class=\"container-fluid\">{$feedHTML}</div>";

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}