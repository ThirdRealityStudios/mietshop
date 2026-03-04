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

    public function show(array $companyData, array $accountData) : void
    {
        $cutFromLength = 30;

        if(strlen($accountData["Email"]) > $cutFromLength)
        {
            $accountEmailLimited = substr($accountData["Email"], 0, $cutFromLength);
            $accountEmailLimited .= "...";
        }
        else
        {
            $accountEmailLimited = $accountData["Email"];
        }

        $accountEmailSafe = htmlspecialchars($accountEmailLimited, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $feedContent =
        "
            <div class=\"container-fluid\">
                <div class=\"row\">
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
                                    <span>Sie sind: {$accountData["PermissionRole"]}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";

        $feedHTML = $this->globalView->genFeed(null, $feedContent);

        $body = "<div class=\"container-fluid\">{$feedHTML}</div>";

        $this->globalView->show($companyData, $body);
    }
}