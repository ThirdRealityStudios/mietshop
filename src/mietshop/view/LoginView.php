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

        $feedContent =
        "
        <div class=\"container-fluid\">
            <div class=\"row mb-5\">
                <div class=\"col-12 text-center fs-1\">
                    <span class=\"fw-bold\">Anmelden</span>&nbsp;<div class=\"d-inline-block\" style=\"background-image: url('/img/symbol/box-arrow-in-right.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\">&nbsp;&nbsp;&nbsp;&nbsp;</div>
                </div>
            </div>
            <form class=\"form\" action=\"/?view=login\" method=\"post\">
                <div class=\"mb-3\">
                    <label for=\"Email\" class=\"form-label$inputLabelsInvalidClass\">E-Mail: </label>
                    <input class=\"form-control$inputFieldsInvalidClass\" type=\"email\" placeholder=\"E-Mail-Adresse eingeben ...\" id=\"Email\" name=\"Email\" required>
                </div>
                <div class=\"mb-3\">
                    <label for=\"Password\" class=\"form-label$inputLabelsInvalidClass\">Passwort:</label>
                    <input class=\"form-control$inputFieldsInvalidClass\" type=\"password\" id=\"Password\" name=\"Password\" placeholder=\"Passwort eingeben ...\" required>
                </div>
                <div class=\"row text-left mb-3\">
                    <div class=\"col-12\">
                        <span class=\"fs-6 text-danger fw-bold\">{$safeOptionalErrorMessage}</span>
                    </div>
                </div>
                <div class=\"mb-3\">
                    <input class=\"btn btn-primary\" type=\"submit\" value=\"Anmelden\">
                </div>
            </form>
        </div>    
        ";

        $feedHTML = $this->globalView->genFeed(null, $feedContent);

        $body = "<div class=\"container-fluid\">$feedHTML</div>";

        $this->globalView->show($companyData, $appearanceData, $body);
    }
}