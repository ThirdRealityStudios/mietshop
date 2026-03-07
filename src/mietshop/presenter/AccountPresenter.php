<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAccountPresenter;
use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\ILoginView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AccountPresenter implements IAccountPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAccountView $accountView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAccountView $accountView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->accountView = $accountView;
        $this->errorView = $errorView;
    }

    private function redirectToLogin() : void
    {
        header("Location: /?view=login");
        exit(0);
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();
        $queryViewValue = $httpRequest->getQueryValue("view");
        $queryActionValue = $httpRequest->getQueryValue("action");

        if(!empty($companyData) && is_string($queryViewValue) && strcmp($queryViewValue, "account") === 0)
        {
            if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Am I logged in yet?
            {
                // Then I am logged in (at least judging server-side - maybe the account does not exist anymore because an admin deleted it)

                if(is_string($queryActionValue) && strcmp($queryActionValue, "logout") === 0) // Does the user want to logout?
                {
                    session_destroy();
                    $this->redirectToLogin();
                }
                else // Else the user just wants to see his account page
                {
                    $accountData = $this->accountModel->readAccount($sessionValues["Email"]);

                    if(!empty($accountData)) // Does the login status belong to an existing account still?
                    {
                        $permissionLevel = $accountData["PermissionLevel"];

                        if(key_exists($permissionLevel, IAccountModel::PERMISSION_ROLE))
                        {
                            $determinedRole = IAccountModel::PERMISSION_ROLE[$permissionLevel];

                            $canManageAccounts = $this->accountModel->canManageAccounts($sessionValues["Email"]);

                            $this->accountView->show($companyData, ["Email" => $sessionValues["Email"], "PermissionRole" => $determinedRole, "CanManageAccounts" => $canManageAccounts]);
                        }
                        else
                        {
                            $this->errorView->show($companyData, "Integritätsfehler", "Ihre Befugnisse scheinen ungültig zu sein."); // Else there is a bug because more roles do not exist ("Loading Error")
                            exit(0);
                        }
                    }
                    else // For some reason the account does not exist anymore, although you have a verified session access generally to an account (most likely deleted by admin meanwhile).
                    {
                        $this->errorView->show($companyData, "Integritätsfehler", "Ihre Anmeldung ist nicht mehr gültig. Bitte melden Sie sich erneut an. Sollte das Problem weiterhin bestehen, wenden Sie sich an den Support.");
                        session_destroy();
                    }
                }
            }
            else
            {
                // Then I am not logged in.

                $this->redirectToLogin();
            }
        }
        else
        {
            $this->errorView->show($companyData, "Ungültige Anfrage", "Der angeforderte Dienst ist nicht verfügbar.");
        }
    }
}