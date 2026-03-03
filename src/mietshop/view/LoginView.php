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

    public function show(array $companyData): void
    {
        $feedContent =
        "
        <div class=\"container-fluid\">
            <div class=\"row text-center mb-3\">
                <span class=\"fs-1 fw-bold\">Anmelden</span>
            </div>
            <form class=\"form\" action=\"/?view=login\" method=\"post\">
                <div class=\"mb-3\">
                    <label for=\"Email\" class=\"form-label\">E-Mail: </label>
                    <input class=\"form-control\" type=\"email\" placeholder=\"E-Mail-Adresse eingeben ...\" name=\"Email\" required>
                </div>
                <div class=\"mb-3\">
                    <label for=\"Password\" class=\"form-label\">Passwort:</label>
                    <input class=\"form-control\" type=\"password\" name=\"Password\" placeholder=\"Passwort eingeben ...\" required>
                </div>
                <input class=\"btn btn-primary mb-3\" type=\"submit\" value=\"Anmelden\">
            </form>
        </div>    
        ";

        $feedHTML = $this->globalView->genFeed(null, $feedContent);

        $body = "<div class=\"container-fluid\">$feedHTML</div>";

        $this->globalView->show($companyData, $body);
    }
}