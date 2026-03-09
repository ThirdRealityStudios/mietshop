<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountManagementPresenter;
use src\mietshop\contracts\IAccountManagementView;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AccountManagementPresenter implements IAccountManagementPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAccountManagementView $accountManagementView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAccountManagementView $accountManagementView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->accountManagementView = $accountManagementView;
        $this->errorView = $errorView;
    }

    /**
     * Filters all elements (roles, e.g. the element 1 => "Moderator") from a given permission role array.
     * The given permission role array should look like (!),
     * as described in IAccountModel::PERMISSION_ROLE.
     *
     * @param int $maxPermissionLevel The maximum permission level to allow.
     * @param array $permissionRoles The provided permission roles.
     * @return array The array of permission roles (filtered and also sorted ascending by permission level).
     */
    private function filterDeniedRoles(int $maxPermissionLevel, array $permissionRoles) : array
    {
        sort($permissionRoles, SORT_NUMERIC); // Roles starting with lowest, ending with the highest (admin usually).

        $permissionRoleLevels = array_keys($permissionRoles);

        $onlyPermittedPermissionLevels = [];

        // Remove permission levels you can't set (will be checked anyway in backend but anyway important to remove for best user experience and no error messages)
        for($i = 0; $i < sizeof($permissionRoleLevels); $i++)
        {
            $existingPermissionLevel = $permissionRoleLevels[$i];

            if($maxPermissionLevel >= $existingPermissionLevel)
            {
                // Add suitable permission role
                $onlyPermittedPermissionLevels[$existingPermissionLevel] = $permissionRoles[$existingPermissionLevel];
            }
        }

        return $onlyPermittedPermissionLevels;
    }

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Is the visitor logged in?
        {
            $accountFetchData = $this->accountModel->readAccount($sessionValues["Email"]);

            // But does the account still exist? Maybe the session is older and the account does not exist anymore (e.g. admin deleted it or owner).
            // Also check (if the account exists) if the user may manage accounts to give access.
            if(!empty($accountFetchData) && isset($accountFetchData["PermissionLevel"]) && is_numeric($accountFetchData["PermissionLevel"]) && $this->accountModel->canManageAccounts($sessionValues["Email"]))
            {
                $supervisorEmail = $sessionValues["Email"];
                $supervisorPermissionLevel = $accountFetchData["PermissionLevel"];

                $companyData = $this->companyModel->getCompanyData();

                if($request->isRequestParameterMatching("action", "all"))
                {
                    $this->accountManagementView->show($companyData, [], [], "all");
                }
                else if($request->isRequestParameterMatching("action" , "create"))
                {
                    $filteredPermissionRoleArray = $this->filterDeniedRoles($supervisorPermissionLevel, IAccountModel::PERMISSION_ROLE);

                    $potentialPostEmailNewAccountValue = $request->getPOSTValue("emailNewAccount");
                    $potentialPostPasswordNewAccountValue = $request->getPOSTValue("passwordNewAccount");
                    $potentialPostPermissionLevelNewAccountValue = $request->getPOSTValue("permissionLevelNewAccount");
                    $potentialPostPasswordSupervisorValue = $request->getPOSTValue("passwordSupervisor");

                    // Does the user (supervisor) want to create a new account? (= a POST action)
                    if(is_string($potentialPostEmailNewAccountValue) && is_string($potentialPostPasswordNewAccountValue) && is_numeric($potentialPostPermissionLevelNewAccountValue) && is_string($potentialPostPasswordSupervisorValue))
                    {
                        $responseCode = $this->accountModel->createAccount($supervisorEmail, $potentialPostPasswordSupervisorValue, $potentialPostEmailNewAccountValue, $potentialPostPasswordNewAccountValue, $potentialPostPermissionLevelNewAccountValue);

                        $this->accountManagementView->show($companyData, [], [], "create", $responseCode);
                    }
                    else // Does the supervisor only wants to see the form page? (always a GET action)
                    {
                        $this->accountManagementView->show($companyData, [], $filteredPermissionRoleArray, "create");
                    }
                }
                else if($request->isRequestParameterMatching("action", "read"))
                {
                    $pagesAmount = $this->accountModel->readAmountOfPages();
                    $pageNonIndexed = $request->getQueryValue("page");

                    if(is_numeric($pageNonIndexed) && $pageNonIndexed >= 1 && $pageNonIndexed <= $pagesAmount)
                    {
                        $accountResults = $this->accountModel->readAccounts($pageNonIndexed-1);

                        $this->accountManagementView->show($companyData, $accountResults, [], "read", 0, $pageNonIndexed-1, $pagesAmount);
                    }
                    else
                    {
                        // Invalid page number requested. Instead, show the first page by default:

                        $accountResults = $this->accountModel->readAccounts(0);
                        $this->accountManagementView->show($companyData, $accountResults, [], "read", 0, 0, $pagesAmount);
                    }
                }
                else if($request->isRequestParameterMatching("action", "update"))
                {
                    $filteredPermissionRoleArray = $this->filterDeniedRoles($supervisorPermissionLevel, IAccountModel::PERMISSION_ROLE);

                    $this->accountManagementView->show($companyData, [], $filteredPermissionRoleArray, "update");
                }
                else if($request->isRequestParameterMatching("action", "delete"))
                {
                    $filteredPermissionRoleArray = $this->filterDeniedRoles($supervisorPermissionLevel, IAccountModel::PERMISSION_ROLE);

                    $this->accountManagementView->show($companyData, [], $filteredPermissionRoleArray, "delete");
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