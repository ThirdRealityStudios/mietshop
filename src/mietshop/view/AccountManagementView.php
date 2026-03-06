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

    private function genTableAccountRow(string $email, int $permissionLevel)
    {
        $emailSafeHTML = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

        switch($permissionLevel)
        {
            case IAccountModel::ROLE_ADMIN:
                $permissionRole = "Admin";
                $bootstrapClass = "table-danger";
            break;

            case IAccountModel::ROLE_OWNER:
                $permissionRole = "Owner";
                $bootstrapClass = "table-warning";
            break;

            case IAccountModel::ROLE_SUPPORTER:
                $permissionRole = "Supporter";
                $bootstrapClass = "table-info";
            break;

            case IAccountModel::ROLE_MODERATOR:
                $permissionRole = "Moderator";
                $bootstrapClass = "table-primary";
            break;

            case IAccountModel::ROLE_GUEST:
                $permissionRole = "Guest";
                $bootstrapClass = "table-light";
            break;

            default:
                $permissionRole = "Invalid";
                $bootstrapClass = "table-secondary";
        }

        return "<tr class=\"$bootstrapClass text-break\"><td>$emailSafeHTML</td><td>$permissionRole</td></tr>";
    }

    public function show(array $companyData, array $accountResults, string $action, int $pageIndex = 0, int $pagesAmount = 0): void
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
                    <div class=\"container-fluid\">
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
                $feedContentHTML =
                "
                    create
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
                <div class=\"container-fluid\">
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
                            <table class=\"table table-bordered w-100\">
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