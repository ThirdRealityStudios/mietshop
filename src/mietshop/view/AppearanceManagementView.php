<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;

class AppearanceManagementView implements IAppearanceManagementView
{
    private IGlobalView $globalView;
    private IErrorView $errorView;

    public function __construct(IGlobalView $globalView, IErrorView $errorView)
    {
        $this->globalView = $globalView;
        $this->errorView = $errorView;
    }

    private function showInvalidRequest(array $companyData)
    {
        $this->errorView->show($companyData, "Ungültige Anfrage", "Aktualisierung fehlgeschlagen.");
    }

    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0): void
    {
        if(empty($companyData))
        {
            return;
        }
        else
        {
            if(strcmp($action, "all") === 0)
            {
                $feedContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-12\">
                            <span class=\"fs-2 fw-bold\">Darstellung verwalten</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=color\">Farben ändern 🎨️</a>
                        </div>
                    </div>
                    <div class=\"row mb-5\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=image\">Grafiken ändern 📸</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=account\">Zurück</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else if(strcmp($action, "update") === 0)
            {
                if(strcmp($target, "color") === 0)
                {
                    if(strcmp($element, "frame") === 0)
                    {
                        // TODO Show if successful or not or whatever ...
                    }
                    else if(strcmp($element, "window") === 0)
                    {

                    }
                    else if(strcmp($element, "background") === 0)
                    {

                    }
                    else if(strcmp($element, "text") === 0)
                    {

                    }
                    else if(strcmp($element, "link") === 0)
                    {

                    }
                    else if(strcmp($element, "interesting") === 0)
                    {

                    }
                    else if(strcmp($element, "uninteresting") === 0)
                    {

                    }
                    else if(strcmp($element, "actionExecute") === 0)
                    {

                    }
                    else if(strcmp($element, "actionAbort") === 0)
                    {

                    }
                    else
                    {
                        $this->showInvalidRequest($companyData);
                    }
                }
                else if(strcmp($target, "image") === 0)
                {

                }
                else
                {
                    $this->errorView->show($companyData, "", "");
                }

                // Is only the normal account permission update page requested? (without field validation or hints)
                // Or did something go wrong after submit? (a field was incorrect or permission update failed generally)
                if($messageValue === 0)
                {
                    $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel($permissionRoles);

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-12\">
                                <span class=\"fs-2 fw-bold\">Berechtigung aktualisieren</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"update\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$emailAccountLabelBS\" for=\"emailAccount\">E-Mail des Kontos: </label>
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailAccount\" id=\"emailAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailAccount\" class=\"p-0$emailAccountLabelBS\">$emailAccountLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            $selectPermissionLevelHTML
                                        </div>
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$passwordSupervisorLabelBS\" for=\"passwordSupervisor\">Ihr Passwort zur Freigabe: </label>
                                            <input value=\"\" class=\"form-control w-100$passwordSupervisorFieldBS\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort zur Autorisierung\">
                                            <div class=\"row mt-2\">
                                                <label for=\"passwordSupervisor\" class=\"p-0 fw-bold$passwordSupervisorLabelBS\">$passwordSupervisorLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-2\">
                                            <input type=\"submit\" class=\"btn btn-success w-100\" value=\"Berechtigung anpassen\">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-danger w-100\" href=\"/?view=accounts&action=all\">Abbrechen</a>
                            </div>
                        </div>
                    </div>
                    ";
                }
                else if($messageValue === -1) // Do I have insufficient permissions to update the permissions of this account? (most likely because a higher permission level was provided than the supervisor has)
                {
                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-2\" style=\"background-image: url('/img/symbol/person-fill-slash-danger.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                            <div class=\"col-10\">
                                <span class=\"fs-2 fw-bold text-danger\">Unzureichende Befugnisse</span>
                            </div>
                        </div>
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                <span class=\"fs-6 w-100\">Es scheint so, als sei die Anfrage ungültig, da z.B. die vergebene Berechtigung höher ist als Ihre eigene oder weil das Zielkonto durch gleiche oder höhere Rechte geschützt ist vor Änderungen.</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-secondary w-100\" href=\"/?view=accounts&action=all\">Zur Übersicht</a>
                            </div>
                        </div>
                    </div>
                    ";
                }
                else if($messageValue === 1) // Was the account permission update successful?
                {
                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-2\" style=\"background-image: url('/img/symbol/person-fill-check-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                            <div class=\"col-10\">
                                <span class=\"fs-2 fw-bold text-success\">Berechtigung erfolgreich aktualisiert</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-success w-100\" href=\"/?view=accounts&action=update\">Weitere Berechtigung ändern</a>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-secondary w-100\" href=\"/?view=accounts&action=all\">Zur Übersicht</a>
                            </div>
                        </div>
                    </div>
                    ";
                }
                else
                {
                    $feedContentHTML =
                    "
                        
                    ";
                }
            }
            else
            {
                $feedContentHTML =
                "
                    Unknown action
                ";
            }

            $feedHTML = $this->globalView->genFeed(null, $feedContentHTML);

            $body =
            "
            <div class=\"container-fluid\">
                $feedHTML
            </div>
            ";

            $this->globalView->show($companyData, $body);
        }
    }
}