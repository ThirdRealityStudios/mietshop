<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAccountManagementView;
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

    }

    public function show(array $companyData, array $accountResults): void
    {
        if(empty($companyData))
        {
            $feedContentHTML = "Loading error";
        }
        else if(empty($accountResults))
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
        else
        {
            $feedContentHTML =
            "
                <div class=\"container-fluid\">
                    <div class=\"row mt-3 mb-3\">
                        <div class=\"col-12\">
                            <span class=\"fs-2 fw-bold\">Kontenübersicht</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary\" href=\"/?view=account\">Zurück ⬅️</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <table class=\"table table-bordered table-responsive\">
                                <tr class=\"table-dark\">
                                    <th>E-Mail</th>
                                    <th>Benutzer-Level</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
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