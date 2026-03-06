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
        $companyData = $this->companyModel->getCompanyData();

        $action = $request->getQueryValue("action");

        if(is_string($action))
        {
            if(strcmp($action, "all") === 0)
            {
                $this->accountManagementView->show($companyData, [], "all");
            }
            else if(strcmp($action, "create") === 0)
            {
                $this->accountManagementView->show($companyData, [], "create");
            }
            else if(strcmp($action, "read") === 0)
            {
                $pagesAmount = $this->accountModel->readAmountOfPages();
                $pageNonIndexed = $request->getQueryValue("page");

                if(is_numeric($pageNonIndexed) && $pageNonIndexed >= 1 && $pageNonIndexed <= $pagesAmount)
                {
                    $accountResults = $this->accountModel->readAccounts($pageNonIndexed-1);

                    $this->accountManagementView->show($companyData, $accountResults, "read", $pageNonIndexed-1, $pagesAmount);
                }
                else
                {
                    // Invalid page number requested. Instead, show the first page by default:

                    $accountResults = $this->accountModel->readAccounts(0);
                    $this->accountManagementView->show($companyData, $accountResults, "read", 0, $pagesAmount);
                }
            }
            else if(strcmp($action, "update") === 0)
            {
                $this->accountManagementView->show($companyData, [], "update");
            }
            else if(strcmp($action, "delete") === 0)
            {
                $this->accountManagementView->show($companyData, [], "delete");
            }
            else
            {
                // This feature does not exist / not implemented.
                $this->errorView->show($companyData, "Angeforderte Funktion ungültig", "Die angeforderte Funktion existiert nicht.");
            }
        }
        else
        {
            // Invalid request because the query field "action" is always needed to specify what possible actions should be shown exactly.
            $this->errorView->show($companyData, "Ungültige Anfrage", "Diese Anfrage wird nicht akzeptiert.");
        }
    }
}