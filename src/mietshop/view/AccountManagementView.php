<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAccountManagementView;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class AccountManagementView implements IAccountManagementView
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

        return "<tr class=\"$bootstrapClass darker-interact text-break\"><td>$emailSafeHTML</td><td>$permissionRole</td></tr>";
    }

    /**
     * Generates and returns a submittable form select list which contains all permission roles in ascending order
     * (lowest role first at the top, highest role - usually admin - last in list).
     *
     * @param array $permissionRoles
     * @param array $appearanceData
     * @return string Form select HTML with all permission roles in ascending order.
     */
    private function genHTMLSelectPermissionLevel(array $permissionRoles, array $appearanceData) : string
    {
        $labelSelectTitle = $this->htmlFactory->genLabelText($appearanceData, "Benutzer-Level auswählen: ", "permissionLevelOptions");

        $selectHTML = "$labelSelectTitle<select class=\"form-select\" name=\"permissionLevelOptions\" id=\"permissionLevelOptions\">";

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

    private function genAccountManagementButton(array $appearanceData, string $title, string $href) : string
    {
        return $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], $title, $href);
    }

    private function genActionResultMessage(array $appearanceData, string $svgPath, string $action, string $title, string $description = "") : string
    {
        $svgForText = $this->htmlFactory->genTextSVG($svgPath);
        $spanTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fw-bold", "inline-block", "lh-1" ], "$title&nbsp;$svgForText");
        $spanDescription = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "w-100" ], $description);

        $aRepeatActionButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Zur Übersicht", "/?view=accounts&action=all");
        $aReturnButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Aktion wiederholen", "/?view=accounts&action=$action");

        $descriptionHTML = (!empty($description)) ? "<div class=\"row mb-5\"><div class=\"col-12\">$spanDescription</div></div>" : "";

        return
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row mb-5 fs-2\">
                $spanTitle
            </div>
            $descriptionHTML
            <div class=\"row mb-3\">
                <div class=\"col-12\">
                    $aReturnButton
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    $aRepeatActionButton
                </div>
            </div>
        </div>
        ";
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
                $spanManageAccount = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Konten verwalten");
                $aButtonCreateAccount = $this->genAccountManagementButton($appearanceData, "Konto erstellen ✏️", "/?view=accounts&action=create");
                $aButtonAllAccounts = $this->genAccountManagementButton($appearanceData, "Alle Konten ansehen 💾", "/?view=accounts&action=read");
                $aButtonUpdateAccountPermission = $this->genAccountManagementButton($appearanceData, "Berechtigungen bearbeiten 🚦", "/?view=accounts&action=update");
                $aButtonDeleteAccount = $this->genAccountManagementButton($appearanceData, "Konto löschen ❌", "/?view=accounts&action=delete");
                $aButtonReturn = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Zurück", "/?view=account");

                $feedContentHTML =
                "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                $spanManageAccount
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                $aButtonCreateAccount
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                $aButtonAllAccounts
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                $aButtonUpdateAccountPermission
                            </div>
                        </div>
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                $aButtonDeleteAccount
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12\">
                                $aButtonReturn
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
                    $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel($permissionRoles, $appearanceData);

                    if($messageValue === -1)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelText = "Bitte geben Sie eine gültige E-Mail-Adresse ein!";
                    }
                    else if($messageValue === -4)
                    {
                        $emailAccountFieldBS = " is-invalid";
                        $emailAccountLabelText = "Bitte geben Sie einen gültigen, nicht existierenden Account ein!";
                    }
                    else
                    {
                        $emailAccountFieldBS = "";
                        $emailAccountLabelText = "Die E-Mail-Adresse hat außer der Benutzerkennung keine weitere Funktion.";
                    }

                    if($messageValue === -2)
                    {
                        $passwordNewAccountFieldBS = " is-invalid";
                        $passwordNewAccountLabelText = "Das Passwort darf nicht leer sein!";
                    }
                    else
                    {
                        $passwordNewAccountFieldBS = "";
                        $passwordNewAccountLabelText = "Beliebige Zeichen für Passwort erlaubt - darf nur nicht leer sein.";
                    }

                    if($messageValue === -3)
                    {
                        $passwordSupervisorFieldBS = " is-invalid";
                        $passwordSupervisorLabelText = "Bitte autorisieren Sie die Aktion mit Ihrem gültigen Kennwort";
                    }
                    else
                    {
                        $passwordSupervisorFieldBS = "";
                        $passwordSupervisorLabelText = "&nbsp;";
                    }

                    $spanCreateAccount = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Konto erstellen");
                    $labelEmail = $this->htmlFactory->genLabelText($appearanceData, "Email: ", "emailNewAccount");
                    $labelEmailHint = $this->htmlFactory->genLabelText($appearanceData, $emailAccountLabelText, "emailNewAccount");

                    $labelPassword = $this->htmlFactory->genLabelText($appearanceData, "Passwort: ", "passwordNewAccount");
                    $labelPasswordHint = $this->htmlFactory->genLabelText($appearanceData, $passwordNewAccountLabelText, "passwordNewAccount");

                    $labelPasswordSupervisor = $this->htmlFactory->genLabelText($appearanceData, "Ihr Passwort zur Freigabe: ", "passwordSupervisor");
                    $labelPasswordSupervisorHint = $this->htmlFactory->genLabelText($appearanceData, $passwordSupervisorLabelText, "passwordNewAccount");

                    $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "ActionExecute" ], $appearanceData, [], [ "w-100", "btn" ], "", "value=\"Konto anlegen\" type=\"submit\"");
                    $aAbortButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "ActionAbort" ], $appearanceData, [], [ "w-100", "btn" ], "Abbrechen", "/?view=accounts&action=all");

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                $spanCreateAccount
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"create\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            $labelEmail
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailNewAccount\" id=\"emailNewAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                $labelEmailHint
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            $labelPassword
                                            <input value=\"\" class=\"form-control w-100$passwordNewAccountFieldBS\" type=\"password\" name=\"passwordNewAccount\" id=\"passwordNewAccount\" placeholder=\"Passwort\">
                                            <div class=\"row mt-2\">
                                                $labelPasswordHint
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            $selectPermissionLevelHTML
                                        </div>
                                        <div class=\"row mb-3\">
                                            $labelPasswordSupervisor
                                            <input value=\"\" class=\"form-control w-100$passwordSupervisorFieldBS\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort zur Autorisierung\">
                                            <div class=\"row mt-2\">
                                                $labelPasswordSupervisorHint
                                            </div>
                                        </div>
                                        <div class=\"row mb-2\">
                                            $inputSubmitButton
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12\">
                                $aAbortButton
                            </div>
                        </div>
                    </div>
                    ";
                }
                else if($messageValue === -5) // Do I have insufficient permissions to create this account? (most likely because a higher permission level was provided than the creator)
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/person-fill-slash.svg", "create", "Unzureichende Befugnisse", "Es scheint so, als sei die Anfrage ungültig, da versucht wurde ein Account zu erstellen mit einem Benutzer-Level. das höher ist als Ihr eigenes.");
                }
                else if($messageValue === 1) // Was the account creation successful?
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/person-fill-check.svg", "create", "Konto erfolgreich erstellt");
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

                $spanTitleAllAccounts = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Kontenübersicht");
                $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "btn" ], "", "type=\"submit\" value=\"Seite aufrufen\"");

                $aReturnButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "w-100", "btn" ], "Zurück", "/?view=accounts&action=all");

                $kbdPageInfo = $this->htmlFactory->genNonSelfClosingElement("kbd", [ "Frame" ], $appearanceData, [], [ "bg-white", "border", "border-1", "text-black", "fw-bold", "rounded-2" ], "Seite {$pageNonIndexed} / {$pagesAmount}");

                $feedContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mb-5\">
                        <div class=\"col-12\">
                            $spanTitleAllAccounts
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
                                            $inputSubmitButton
                                        </div>
                                        <div class=\"col-4\">
                                        </div>
                                    </div>
                                    <div class=\"row mb-3\">
                                        <div class=\"col-12\">
                                            $kbdPageInfo
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-12\">
                            $aReturnButton
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
                    $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel($permissionRoles, $appearanceData);

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

                    $spanTitleUpdatePermission = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Berechtigung aktualisieren");

                    $labelEmail = $this->htmlFactory->genLabelText($appearanceData, "E-Mail des Kontos: ", "emailAccount");
                    $labelPasswordSupervisor = $this->htmlFactory->genLabelText($appearanceData, "Ihr Passwort zur Freigabe: ", "passwordSupervisor");

                    $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "ActionExecute" ], $appearanceData, [], [ "w-100", "btn" ], "", "value=\"Berechtigung anpassen\" type=\"submit\"");
                    $aAbortButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "ActionAbort" ], $appearanceData, [], [ "w-100", "btn" ], "Abbrechen", "/?view=accounts&action=all");

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                $spanTitleUpdatePermission
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"update\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            $labelEmail
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailAccount\" id=\"emailAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailAccount\" class=\"p-0$emailAccountLabelBS\">$emailAccountLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            $selectPermissionLevelHTML
                                        </div>
                                        <div class=\"row mb-3\">
                                            $labelPasswordSupervisor
                                            <input value=\"\" class=\"form-control w-100$passwordSupervisorFieldBS\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort zur Autorisierung\">
                                            <div class=\"row mt-2\">
                                                <label for=\"passwordSupervisor\" class=\"p-0 fw-bold$passwordSupervisorLabelBS\">$passwordSupervisorLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-2\">
                                            $inputSubmitButton
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12\">
                                $aAbortButton
                            </div>
                        </div>
                    </div>
                    ";
                }
                else if($messageValue === -4) // Is there a general processing error? Maybe it is a bug?
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/cpu-fill.svg", "all", "Allgemeiner Verarbeitungsfehler", "Es scheint so, als wäre die Verarbeitung gerade nicht möglich. Bitte versuchen Sie es später erneut!");
                }
                else if($messageValue === -5) // Do I have insufficient permissions to update the permissions of this account? (most likely because a higher permission level was provided than the supervisor has)
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/person-fill-slash.svg", "update", "Unzureichende Befugnisse", "Es scheint so, als sei die Anfrage ungültig, da z.B. die vergebene Berechtigung höher ist als Ihre eigene oder weil das Zielkonto durch gleiche oder höhere Rechte geschützt ist vor Änderungen.");
                }
                else if($messageValue === 1) // Was the account permission update successful?
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/person-fill-check.svg", "update", "Berechtigung erfolgreich aktualisiert");
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

                    $spanTitleDeleteAccount = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Konto löschen");

                    $labelEmail = $this->htmlFactory->genLabelText($appearanceData, "E-Mail des Kontos: ", "emailAccount");
                    $labelPasswordSupervisor = $this->htmlFactory->genLabelText($appearanceData, "Ihr Passwort zur Freigabe: ", "passwordSupervisor");

                    $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "ActionExecute" ], $appearanceData, [], [ "w-100", "btn" ], "", "value=\"Konto endgültig löschen\" type=\"submit\"");
                    $aAbortButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "ActionAbort" ], $appearanceData, [], [ "w-100", "btn" ], "Abbrechen", "/?view=accounts&action=all");

                    $feedContentHTML =
                    "
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                $spanTitleDeleteAccount
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <form class=\"form\" action=\"/\" method=\"post\">
                                    <input type=\"hidden\" name=\"view\" value=\"accounts\">
                                    <input type=\"hidden\" name=\"action\" value=\"delete\">
                                    <div class=\"container-fluid\">
                                        <div class=\"row mb-3\">
                                            $labelEmail
                                            <input value=\"\" class=\"form-control w-100$emailAccountFieldBS\" type=\"email\" name=\"emailAccount\" id=\"emailAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailAccount\" class=\"p-0$emailAccountLabelBS\">$emailAccountLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            $labelPasswordSupervisor
                                            <input value=\"\" class=\"form-control w-100$passwordSupervisorFieldBS\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort\">
                                            <div class=\"row mt-2\">
                                                <label for=\"passwordSupervisor\" class=\"p-0 fw-bold$passwordSupervisorLabelBS\">$passwordSupervisorLabelText</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-2\">
                                            $inputSubmitButton
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12\">
                                $aAbortButton
                            </div>
                        </div>
                    </div>
                    ";
                }
                else if($messageValue === -5) // Do I have insufficient permissions to delete the account?
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/person-fill-slash.svg", "delete", "Unzureichende Befugnisse", "Sie scheinen zu wenig Befugnisse zu haben, um diese Aktion durchzuführen.");
                }
                else if($messageValue === 1) // Was the account deletion successful?
                {
                    $feedContentHTML = $this->genActionResultMessage($appearanceData, "/img/symbol/trash-fill.svg", "delete", "Konto erfolgreich gelöscht!");
                }
            }
            else
            {
                $feedContentHTML =
                "
                    unknown action
                ";
            }

            $feedHTML = $this->componentFactory->genFeed($appearanceData, null, $feedContentHTML);

            $this->globalView->show($companyData, $appearanceData, $feedHTML);
        }
    }
}