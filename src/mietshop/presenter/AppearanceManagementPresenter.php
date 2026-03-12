<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAppearanceManagementPresenter;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AppearanceManagementPresenter implements IAppearanceManagementPresenter
{
    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Is the visitor logged in?
        {
            $accountFetchData = $this->accountModel->readAccount($sessionValues["Email"]);

            // But does the account still exist? Maybe the session is older and the account does not exist anymore (e.g. admin deleted it or owner).
            // Also check (if the account exists) if the user may manage accounts to give access.
            if(!empty($accountFetchData) && $this->accountModel->isSupervisor($sessionValues["Email"]))
            {
                $supervisorEmail = $sessionValues["Email"];
                $supervisorPermissionLevel = $accountFetchData["PermissionLevel"];

                $companyData = $this->companyModel->getCompanyData();

                if($request->isRequestParameterMatching("action", "all"))
                {
                    $this->accountManagementView->show($companyData, [], [], "all");
                }
                else if($request->isRequestParameterMatching("action", "update"))
                {
                    $filteredPermissionRoleArray = $this->filterDeniedRoles($supervisorPermissionLevel, IAccountModel::PERMISSION_ROLE);

                    $postEmailAccountValue = $request->getPOSTValue("emailAccount");
                    $postPermissionLevelAccountValue = $request->getPOSTValue("permissionLevelOptions");
                    $postPasswordSupervisorValue = $request->getPOSTValue("passwordSupervisor");

                    // Does the user (supervisor) want to create a new account? (= a POST action)
                    if(is_string($postEmailAccountValue) && is_numeric($postPermissionLevelAccountValue) && is_string($postPasswordSupervisorValue))
                    {
                        $responseCode = $this->accountModel->updatePermissionLevel($supervisorEmail, $postPasswordSupervisorValue, $postEmailAccountValue, $postPermissionLevelAccountValue);

                        $this->accountManagementView->show($companyData, [], $filteredPermissionRoleArray, "update", $responseCode);
                    }
                    else // Does the supervisor only wants to see the form page? (always a GET action)
                    {
                        $this->accountManagementView->show($companyData, [], $filteredPermissionRoleArray, "update");
                    }
                }
                else
                {
                    // This feature does not exist / not implemented.
                    $this->errorView->show($companyData, "Angeforderte Funktion ungültig", "Die angeforderte Funktion existiert nicht.");
                }
            }
            else
            {
                // Account does not exist or access denied (missing permissions)
                die("Account does not exist or missing permissions");
            }
        }
        else
        {
            // User not logged in
            die("You are not logged in!");
        }
    }
}