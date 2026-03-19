<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\ILoginView;

class LoginView implements ILoginView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData, array $appearanceData, ?string $optionalErrorMessage): void
    {
        if(is_string($optionalErrorMessage))
        {
            $safeOptionalErrorMessage = htmlspecialchars($optionalErrorMessage, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
            $inputLabelsInvalidClass = " text-danger";
            $inputFieldsInvalidClass = " is-invalid";
        }
        else
        {
            $safeOptionalErrorMessage = "&nbsp;";
            $inputLabelsInvalidClass = "";
            $inputFieldsInvalidClass = "";
        }

        $spanLoginTitle = $this->globalView->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fw-bold" ], "Anmelden");
        $textLoginSymbol = $this->globalView->genTextSVG("/img/symbol/box-arrow-in-right.svg", "white");
        $labelEmail = $this->globalView->genLabelText($appearanceData, "Email:&nbsp;", "Email");
        $labelPassword = $this->globalView->genLabelText($appearanceData, "Passwort:&nbsp;", "Password");
        $aLoginButton = $this->globalView->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "btn" ], "", "type=\"submit\" value=\"Anmelden\"");

        $feedContent =
        "
        <div class=\"container-fluid\">
            <div class=\"row mb-5\">
                <div class=\"col-12 text-center fs-1 lh-1\">
                    $spanLoginTitle&nbsp;$textLoginSymbol
                </div>
            </div>
            <form class=\"row form\" action=\"/?view=login\" method=\"post\">
                <div class=\"container-fluid\">
                    <div class=\"row mb-3\">
                        $labelEmail
                        <input class=\"form-control$inputFieldsInvalidClass\" type=\"email\" placeholder=\"E-Mail-Adresse eingeben ...\" id=\"Email\" name=\"Email\" required>
                    </div>
                    <div class=\"row mb-3\">
                        $labelPassword
                        <input class=\"form-control$inputFieldsInvalidClass\" type=\"password\" id=\"Password\" name=\"Password\" placeholder=\"Passwort eingeben ...\" required>
                    </div>
                    <div class=\"row mb-3 text-left\">
                        <div class=\"col-12\">
                            <span class=\"fs-6 text-danger fw-bold\">{$safeOptionalErrorMessage}</span>
                        </div>
                    </div>
                    <div class=\"row\">
                        $aLoginButton
                    </div>
                </div>
            </form>
        </div>    
        ";

        $feedHTML = $this->globalView->genFeed($appearanceData, null, $feedContent);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}