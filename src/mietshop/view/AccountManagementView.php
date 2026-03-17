<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAccountManagementView;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IGlobalView;

class AccountManagementView implements IAccountManagementView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    private function genTableAccountRow(string $email, int $permissionLevel) : string
    {
        $emailSafeHTML = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        $permissionRole = "&nbsp;";
        $bootstrapClass = "table-secondary";

        $existingPermissionLevels = array_keys(IAccountModel::PERMISSION_ROLE);

        foreach($existingPermissionLevels as $existingPermissionLevel)
        {
            // If an existing permission level was found which matches the given permission level, then it is valid.
            if($permissionLevel === $existingPermissionLevel)
            {
                $permissionLevel = $existingPermissionLevel;

                $permissionRole = IAccountModel::PERMISSION_ROLE[$existingPermissionLevel];

                if($permissionLevel === max($existingPermissionLevels)) // Usually the admin
                {
                    $bootstrapClass = "table-danger";
                }
                else if($permissionLevel === (max($existingPermissionLevels)-1))
                {
                    $bootstrapClass = "table-warning";
                }
                else
                {
                    $bootstrapClass = "table-secondary";
                }

                break;
            }
        }

        return "<tr class=\"$bootstrapClass darker-hover text-break\"><td>$emailSafeHTML</td><td>$permissionRole</td></tr>";
    }

    /**
     * Generates and returns a submittable form select list which contains all permission roles in ascending order
     * (lowest role first at the top, highest role - usually admin - last in list).
     *
     * @param array $permissionRoles
     * @return string Form select HTML with all permission roles in ascending order.
     */
    private function genHTMLSelectPermissionLevel(array $permissionRoles) : string
    {
        $selectHTML = "<label class=\"form-label p-0\" for=\"permissionLevelOptions\">Benutzer-Level auswählen: </label><select class=\"form-select\" name=\"permissionLevelOptions\" id=\"permissionLevelOptions\">";

        // Due to implementation detail, there can be "gaps" between permission levels in the array.
        // Hence, reading all permission levels step-by-step is safer.
        $permissionLevelKeys = array_keys($permissionRoles);

        for($i = 0; $i < sizeof($permissionLevelKeys); $i++)
        {
            $permissionLevel = $permissionLevelKeys[$i];

            $permissionRoleName = $permissionRoles[$permissionLevel];
            $permissionRoleSafeHTML = htmlspecialchars($permissionRoleName, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

            $selectHTML .= "<option value=\"{$permissionLevel}\">$permissionRoleSafeHTML</option>";
        }

        return "$selectHTML</select>";
    }

    public function show(array $companyData, array $appearanceData, array $accountResults, array $permissionRoles, string $action, int $messageValue = 0, int $pageIndex = 0, int $pagesAmount = 0): void
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
                                <span class=\"fs-2 fw-bold\">Konten verwalten</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=create\">Konto erstellen ✏️</a>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=read\">Alle Konten ansehen 💾</a>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=update\">Berechtigungen bearbeiten 🚦</a>
                            </div>
                        </div>
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=delete\">Konto löschen ❌</a>
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
            else if(strcmp($action, "create") === 0)
            {
                // Is only the normal account creation page requested? (without field validation or hints)
                // Or did something go wrong after submit? (a field was incorrect or creation failed generally)
                if($messageValue >= -4 && $messageValue <= 0)
                {
                    $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel($permissionRoles);

                    if($messageValue === -1)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelBS = " text-danger";
                        $emailAccountLabelText = "Bitte geben Sie eine gültige E-Mail-Adresse ein!";
                    }
                    else if($messageValue === -4)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelBS = " text-danger";
                        $emailAccountLabelText = "Bitte geben Sie einen gültigen, nicht existierenden Account ein!";
                    }
                    else
                    {
                        $emailAccountFieldBS = "";
                        $emailAccountLabelBS = "";
                        $emailAccountLabelText = "Die E-Mail-Adresse hat außer der Benutzerkennung keine weitere Funktion.";
                    }

                    if($messageValue === -2)
                    {
                        $passwordNewAccountFieldBS = " is-invalid";
                        $passwordNewAccountLabelBS = " text-danger";
                        $passwordNewAccountLabelText = "Das Passwort darf nicht leer sein!";
                    }
                    else
                    {
                        $passwordNewAccountFieldBS = "";
                        $passwordNewAccountLabelBS = "";
                        $passwordNewAccountLabelText = "Beliebige Zeichen für Passwort erlaubt - darf nur nicht leer sein.";
                    }

                    if($messageValue === -3)
                    {
                        $passwordSupervisorFieldBS = " is-invalid";
                        $passwordSupervisorLabelBS = " text-danger";
                        $passwordSupervisorLabelText = "Bitte autorisieren Sie die Aktion mit Ihrem gültigen Kennwort";
                    }
                    else
                    {
                        $passwordSupervisorFieldBS = "";
                        $passwordSupervisorLabelBS = "";
                        $passwordSupervisorLabelText = "&nbsp;";
                    }

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-12\">
                                <span class=\"fs-2 fw-bold\">Konto erstellen</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"create\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$emailAccountLabelBS\" for=\"emailNewAccount\">E-Mail: </label>
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailNewAccount\" id=\"emailNewAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailNewAccount\" class=\"p-0$emailAccountLabelBS\">$emailAccountLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$passwordNewAccountLabelBS\" for=\"passwordNewAccount\">Passwort: </label>
                                            <input value=\"\" class=\"form-control w-100$passwordNewAccountFieldBS\" type=\"password\" name=\"passwordNewAccount\" id=\"passwordNewAccount\" placeholder=\"Passwort\">
                                            <div class=\"row mt-2\">
                                                <label for=\"passwordNewAccount\" class=\"p-0$passwordNewAccountLabelBS\">$passwordNewAccountLabelText</label>
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
                                            <input type=\"submit\" class=\"btn btn-success w-100\" value=\"Konto anlegen\">
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
                else if($messageValue === -5) // Do I have insufficient permissions to create this account? (most likely because a higher permission level was provided than the creator)
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
                                <span class=\"fs-6 w-100\">Es scheint so, als sei die Anfrage ungültig, da versucht wurde ein Account zu erstellen mit einem Benutzer-Level. das höher ist als Ihr eigenes.</span>
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
                else if($messageValue === 1) // Was the account creation successful?
                {
                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-2\" style=\"background-image: url('/img/symbol/person-fill-check-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                            <div class=\"col-10\">
                                <span class=\"fs-2 fw-bold text-success\">Konto erfolgreich erstellt</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-success w-100\" href=\"/?view=accounts&action=create\">Weiteres Konto anlegen</a>
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
            }
            else if(strcmp($action, "read") === 0)
            {
                $rowsHTML = "";

                foreach($accountResults as $accountResult)
                {
                    $rowsHTML .= $this->genTableAccountRow($accountResult["Email"], $accountResult["PermissionLevel"]);
                }

                $emptyFillRows = (sizeof($accountResults) - IAccountModel::ACCOUNTS_PER_PAGE) * (-1);

                if($emptyFillRows > 0)
                {
                    $rowsHTML .= str_repeat($this->genTableAccountRow("", -1), $emptyFillRows);
                }

                $pageNonIndexed = $pageIndex+1;

                $feedContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-12\">
                            <span class=\"fs-2 fw-bold\">Kontenübersicht</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <table style=\"table-layout: fixed;\" class=\"table table-responsive text-break\">
                                <tr class=\"table-dark\">
                                    <th class=\"w-80\">E-Mail</th>
                                    <th class=\"w-20\">Benutzer-Level</th>
                                </tr>
                                $rowsHTML
                            </table>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <form class=\"form\" action=\"/\" method=\"get\">
                                <div class=\"container-fluid p-0\">
                                    <div class=\"row align-items-end mb-4\">
                                        <div class=\"col-4\">
                                            <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                            <input type=\"hidden\" name=\"action\" value=\"read\">
                                            <input class=\"form-control\" type=\"number\" value=\"$pageNonIndexed\" min=\"1\" max=\"{$pagesAmount}\" placeholder=\"Seitenzahl\" name=\"page\">
                                        </div>
                                        <div class=\"col-4\">
                                            <input class=\"btn btn-primary\" type=\"submit\" value=\"Seite aufrufen\">
                                        </div>
                                        <div class=\"col-4\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            <kbd class=\"bg-white border border-1 border-primary text-black fw-bold rounded-2\">Seite {$pageNonIndexed} / {$pagesAmount}</kbd>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=accounts&action=all\">Zurück</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else if(strcmp($action, "update") === 0)
            {
                // Is only the normal account permission update page requested? (without field validation or hints)
                // Or did something go wrong after submit? (a field was incorrect or permission update failed generally)
                if($messageValue === -3 || $messageValue === -2 || $messageValue === 0)
                {
                    $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel($permissionRoles);

                    if($messageValue === -2)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelBS = " text-danger";
                        $emailAccountLabelText = "Bitte geben Sie einen gültigen Account ein!";
                    }
                    else
                    {
                        $emailAccountFieldBS = "";
                        $emailAccountLabelBS = "";
                        $emailAccountLabelText = "";
                    }

                    if($messageValue === -3)
                    {
                        $passwordSupervisorFieldBS = " is-invalid";
                        $passwordSupervisorLabelBS = " text-danger";
                        $passwordSupervisorLabelText = "Bitte autorisieren Sie die Aktion mit Ihrem gültigen Kennwort";
                    }
                    else
                    {
                        $passwordSupervisorFieldBS = "";
                        $passwordSupervisorLabelBS = "";
                        $passwordSupervisorLabelText = "&nbsp;";
                    }

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
                else if($messageValue === -4) // Is there a general processing error? Maybe it is a bug?
                {
                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-2\" style=\"background-image: url('/img/symbol/cpu-fill-danger.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                            <div class=\"col-10\">
                                <span class=\"fs-2 fw-bold text-danger\">Allgemeiner Verarbeitungsfehler</span>
                            </div>
                        </div>
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                <span class=\"fs-6 w-100\">Es scheint so, als wäre die Verarbeitung gerade nicht möglich. Bitte versuchen Sie es später erneut!</span>
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
                else if($messageValue === -5) // Do I have insufficient permissions to update the permissions of this account? (most likely because a higher permission level was provided than the supervisor has)
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
            }
            else if(strcmp($action, "delete") === 0)
            {
                // Is only the normal account permission update page requested? (without field validation or hints)
                // Or did something go wrong after submit? (a field was incorrect or permission update failed generally)
                if($messageValue === -1 || $messageValue === -3 || $messageValue === 0)
                {
                    if($messageValue === -1)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelBS = " text-danger";
                        $emailAccountLabelText = "Bitte geben Sie einen gültigen zu löschenden Account ein!";
                    }
                    else
                    {
                        $emailAccountFieldBS = "";
                        $emailAccountLabelBS = "";
                        $emailAccountLabelText = "";
                    }

                    if($messageValue === -3)
                    {
                        $passwordSupervisorFieldBS = " is-invalid";
                        $passwordSupervisorLabelBS = " text-danger";
                        $passwordSupervisorLabelText = "Bitte autorisieren Sie die Aktion mit Ihrem gültigen Kennwort";
                    }
                    else
                    {
                        $passwordSupervisorFieldBS = "";
                        $passwordSupervisorLabelBS = "";
                        $passwordSupervisorLabelText = "&nbsp;";
                    }

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-12\">
                                <span class=\"fs-2 fw-bold\">Konto löschen</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"delete\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$emailAccountLabelBS\" for=\"emailAccount\">E-Mail des Kontos: </label>
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailAccount\" id=\"emailAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailAccount\" class=\"p-0$emailAccountLabelBS\">$emailAccountLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0$passwordSupervisorLabelBS\" for=\"passwordSupervisor\">Ihr Passwort zur Freigabe: </label>
                                            <input value=\"\" class=\"form-control w-100$passwordSupervisorFieldBS\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort\">
                                            <div class=\"row mt-2\">
                                                <label for=\"passwordSupervisor\" class=\"p-0 fw-bold$passwordSupervisorLabelBS\">$passwordSupervisorLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-2\">
                                            <input type=\"submit\" class=\"btn btn-success w-100\" value=\"Konto endgültig löschen\">
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
                else if($messageValue === -5) // Do I have insufficient permissions to delete the account?
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
                                <span class=\"fs-6 w-100\">Sie scheinen zu wenig Befugnisse zu haben, um diese Aktion durchzuführen.</span>
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
                else if($messageValue === 1) // Was the account deletion successful?
                {
                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mt-3 mb-5\">
                            <div class=\"col-2\" style=\"background-image: url('/img/symbol/trash-fill-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                            <div class=\"col-10\">
                                <span class=\"fs-2 fw-bold text-success\">Konto erfolgreich gelöscht!</span>
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-success w-100\" href=\"/?view=accounts&action=delete\">Weiteres Konto löschen</a>
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
            }
            else
            {
                $feedContentHTML =
                "
                    unknown action
                ";
            }

            $feedHTML = $this->globalView->genFeed($appearanceData, null, $feedContentHTML);

            $body =
            "
            <div class=\"container-fluid\">
                $feedHTML
            </div>
            ";

            $this->globalView->show($companyData, $appearanceData, $body);
        }
    }
}