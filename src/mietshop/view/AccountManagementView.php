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

        $permissionRole = "Invalid";
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
     * @return string Form select HTML with all permission roles in ascending order.
     */
    private function genHTMLSelectPermissionLevel() : string
    {
        $selectHTML = "<label class=\"form-label p-0\" for=\"permissionLevelNewAccount\">Benutzer-Level auswählen: </label><select class=\"form-select\" name=\"permissionLevelNewAccount\" id=\"permissionLevelNewAccount\">";

        // Due to implementation detail, there can be "gaps" between permission levels in the array.
        // Hence, reading all permission levels step-by-step is safer.
        $permissionLevels = array_keys(IAccountModel::PERMISSION_ROLE);

        for($i = 0; $i < sizeof($permissionLevels); $i++)
        {
            $permissionLevel = $permissionLevels[$i];
            $permissionRole = IAccountModel::PERMISSION_ROLE[$i];
            $permissionRoleSafeHTML = htmlspecialchars($permissionRole, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

            $selectHTML .= "<option value=\"{$permissionLevel}\">$permissionRoleSafeHTML</option>";
        }

        return "$selectHTML</select>";
    }

    public function show(array $companyData, array $accountResults, array $permissionRoles, string $action, int $pageIndex = 0, int $pagesAmount = 0): void
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
                        <div class=\"row mt-3 mb-3\">
                            <div class=\"col-12\">
                                <span class=\"fs-2 fw-bold\">Konten verwalten</span>
                            </div>
                        </div>
                        <div class=\"row mb-5\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=account\">Zurück ⬅️</a>
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
                        <div class=\"row mb-3\">
                            <div class=\"col-12\">
                                <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=delete\">Konto löschen ❌</a>
                            </div>
                        </div>
                    </div>
                ";
            }
            else if(strcmp($action, "create") === 0)
            {
                $selectPermissionLevelHTML = $this->genHTMLSelectPermissionLevel();

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
                                            <label class=\"form-label p-0\" for=\"emailNewAccount\">E-Mail: </label>
                                            <input class=\"form-control w-100\" type=\"email\" name=\"emailNewAccount\" id=\"emailNewAccount\" placeholder=\"E-Mail-Adresse\">
                                            <div class=\"row mt-2\">
                                                <label for=\"emailNewAccount\" class=\"p-0 text-danger\">Die E-Mail-Adresse hat außer der Benutzerkennung keine weitere Funktion.</label>
                                            </div>
                                        </div>
                                        <div class=\"row mb-3\">
                                            <label class=\"form-label p-0\" for=\"passwordNewAccount\">Passwort: </label>
                                            <input class=\"form-control w-100\" type=\"password\" name=\"passwordNewAccount\" id=\"passwordNewAccount\" placeholder=\"Passwort\">
                                        </div>
                                        <div class=\"row mb-3\">
                                            $selectPermissionLevelHTML
                                        </div>
                                        <div class=\"row mb-5\">
                                            <label class=\"form-label p-0 text-danger\" for=\"passwordSupervisor\">Ihr Passwort zur Freigabe: </label>
                                            <input class=\"form-control w-100 is-invalid\" type=\"password\" name=\"passwordSupervisor\" id=\"passwordSupervisor\" placeholder=\"Passwort zur Autorisierung\">
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
            else if(strcmp($action, "read") === 0)
            {
                $rowsHTML = "";

                foreach($accountResults as $accountResult)
                {
                    $rowsHTML .= $this->genTableAccountRow($accountResult["Email"], $accountResult["PermissionLevel"]);
                }

                $pageNonIndexed = $pageIndex+1;

                $feedContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-3\">
                        <div class=\"col-12\">
                            <span class=\"fs-2 fw-bold\">Kontenübersicht</span>
                        </div>
                    </div>
                    <div class=\"row mb-5\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=accounts&action=all\">Zurück ⬅️</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <table class=\"table w-100\">
                                <tr class=\"table-dark\">
                                    <th>E-Mail</th>
                                    <th>Benutzer-Level</th>
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
                </div>
                ";

            }
            else if(strcmp($action, "update") === 0)
            {
                $feedContentHTML =
                "
                    update
                ";
            }
            else if(strcmp($action, "delete") === 0)
            {
                $feedContentHTML =
                "
                    delete
                ";
            }
            else
            {
                $feedContentHTML =
                "
                    unknown action
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