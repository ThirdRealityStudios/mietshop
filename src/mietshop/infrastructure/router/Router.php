<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\database\IDatabaseAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\AccountModel;
use src\mietshop\model\CategoryModel;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\AccountManagementPresenter;
use src\mietshop\presenter\AccountPresenter;
use src\mietshop\presenter\ErrorNotFoundPresenter;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\presenter\ImprintPresenter;
use src\mietshop\presenter\LoginPresenter;
use src\mietshop\presenter\PrivacyPolicyPresenter;
use src\mietshop\presenter\TermsOfServicePresenter;
use src\mietshop\view\AccountManagementView;
use src\mietshop\view\AccountView;
use src\mietshop\view\ErrorView;
use src\mietshop\view\GlobalView;
use src\mietshop\view\HomeView;
use src\mietshop\view\ImprintView;
use src\mietshop\view\LoginView;
use src\mietshop\view\PrivacyPolicyView;
use src\mietshop\view\TermsOfServiceView;

class Router implements IRouter
{
    private function showPageNotFoundError(IErrorView $errorView, ICompanyModel $companyModel) : void
    {
        $errorView->show($companyModel->getCompanyData(), "Seite existiert nicht", "Entweder haben Sie sich vertippt oder derzeit gibt es technische Probleme. Bitte versuchen Sie es später erneut.");
    }

    public function route(IHTTPRequest $request, array &$sessionValues, IDatabaseAdapter $databaseAdapter) : void
    {
        $companyModel = new CompanyModel($databaseAdapter);
        $categoryModel = new CategoryModel($databaseAdapter);
        $globalView = new GlobalView();
        $errorView = new ErrorView($globalView);

        if(sizeof($request->getQueryKeys()) === 0)
        {
            $homeView = new HomeView($globalView);
            $homePresenter = new HomePresenter($companyModel, $categoryModel, $homeView, $errorView);
            $homePresenter->process($request);
        }
        else
        {
            $loginView = new LoginView($globalView);
            $accountView = new AccountView($globalView);
            $accountModel = new AccountModel($databaseAdapter);
            $loginPresenter = new LoginPresenter($companyModel, $accountModel, $loginView, $accountView, $errorView);
            $accountPresenter = new AccountPresenter($companyModel, $accountModel, $accountView, $errorView);

            $accountManagementView = new AccountManagementView($globalView);
            $accountManagementPresenter = new AccountManagementPresenter($companyModel, $accountModel, $accountManagementView, $errorView);

            if(in_array("view", $request->getQueryKeys()))
            {
                $viewValue = $request->getQueryValue("view");

                if(strcmp($viewValue, "imprint") === 0)
                {
                    $imprintView = new ImprintView($globalView);
                    $imprintPresenter = new ImprintPresenter($errorView, $imprintView, $companyModel);
                    $imprintPresenter->process();
                }
                else if(strcmp($viewValue, "termsofservice") === 0)
                {
                    $termsOfServiceView = new TermsOfServiceView($globalView);
                    $termsOfServicePresenter = new TermsOfServicePresenter($errorView, $termsOfServiceView, $companyModel);
                    $termsOfServicePresenter->process();
                }
                else if(strcmp($viewValue, "privacypolicy") === 0)
                {
                    $privacyPolicyView = new PrivacyPolicyView($globalView);
                    $privacyPolicyPresenter = new PrivacyPolicyPresenter($privacyPolicyView, $errorView, $companyModel);
                    $privacyPolicyPresenter->process();
                }
                else if(strcmp($viewValue, "login") === 0)
                {
                    // Show the login page or authenticate and then redirect (if the user is yet logged in)
                    $loginPresenter->process($request, $sessionValues);
                }
                else if(strcmp($viewValue, "account") === 0)
                {
                    // Show the account page (will check if you are authorized or logged in yet)
                    $accountPresenter->process($request, $sessionValues);
                }
                else if(strcmp($viewValue, "accounts") === 0)
                {
                    // Show the account management page
                    $accountManagementPresenter->process($request, $sessionValues);
                }
                else
                {
                    $this->showPageNotFoundError($errorView, $companyModel);
                }
            }
            else
            {
                $this->showPageNotFoundError($errorView, $companyModel);
            }
        }
    }
}