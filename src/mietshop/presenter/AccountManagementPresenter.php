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

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Is the visitor authorized?
        {
            $accountFetchData = $this->accountModel->readAccount($sessionValues["Email"]);

            if(!empty($accountFetchData)) // But does the account still exist? Maybe the session is older and the account does not exist anymore (e.g. admin deleted it or owner).
            {
                $companyData = $this->companyModel->getCompanyData();

                $permissionRolesAscending = IAccountModel::PERMISSION_ROLE; // Copy
                sort($permissionRolesAscending, SORT_ASC); // Roles starting with lowest, ending with the highest (admin usually).

                if($request->isRequestParameterMatching("action", "all"))
                {
                    $this->accountManagementView->show($companyData, [], [], "all");
                }
                else if($request->isRequestParameterMatching("action" , "create"))
                {
                    // TODO Determine further query action (show form or process action?)
                    // $this->accountModel->createAccount();
                    $this->accountManagementView->show($companyData, [], $permissionRolesAscending, "create");
                }
                else if($request->isRequestParameterMatching("action", "read"))
                {
                    $pagesAmount = $this->accountModel->readAmountOfPages();
                    $pageNonIndexed = $request->getQueryValue("page");

                    if(is_numeric($pageNonIndexed) && $pageNonIndexed >= 1 && $pageNonIndexed <= $pagesAmount)
                    {
                        $accountResults = $this->accountModel->readAccounts($pageNonIndexed-1);

                        $this->accountManagementView->show($companyData, $accountResults, [], "read", $pageNonIndexed-1, $pagesAmount);
                    }
                    else
                    {
                        // Invalid page number requested. Instead, show the first page by default:

                        $accountResults = $this->accountModel->readAccounts(0);
                        $this->accountManagementView->show($companyData, $accountResults, [], "read", 0, $pagesAmount);
                    }
                }
                else if($request->isRequestParameterMatching("action", "update"))
                {
                    $this->accountManagementView->show($companyData, [], IAccountModel::PERMISSION_ROLE, "update");
                }
                else if($request->isRequestParameterMatching("action", "delete"))
                {
                    $this->accountManagementView->show($companyData, [], [], "delete");
                }
                else
                {
                    // This feature does not exist / not implemented.
                    $this->errorView->show($companyData, "Angeforderte Funktion ungültig", "Die angeforderte Funktion existiert nicht.");
                }
            }
            else
            {

            }
        }
        else
        {

        }
    }
}