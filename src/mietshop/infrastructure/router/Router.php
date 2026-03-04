<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\database\IDatabaseAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\AccountModel;
use src\mietshop\model\CategoryModel;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\ErrorNotFoundPresenter;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\presenter\ImprintPresenter;
use src\mietshop\presenter\AuthPresenter;
use src\mietshop\presenter\PrivacyPolicyPresenter;
use src\mietshop\presenter\TermsOfServicePresenter;
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
    private function chooseErrorNotFoundPresenter(IErrorView $errorView, ICompanyModel $companyModel, IHTTPRequest $request) : void
    {
        $errorPresenter = new ErrorNotFoundPresenter($errorView, $companyModel);
        $errorPresenter->process($request);
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
            $authPresenter = new AuthPresenter($companyModel, $accountModel, $loginView, $accountView, $errorView);

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
                else if(strcmp($viewValue, "login") === 0 || strcmp($viewValue, "account") === 0)
                {
                    // Show the login page or account page, authenticate or redirect (if the user is yet logged in)
                    $authPresenter->process($request, $sessionValues);
                }
                else
                {
                    $this->chooseErrorNotFoundPresenter($errorView, $companyModel, $request);
                }
            }
            else
            {
                $this->chooseErrorNotFoundPresenter($errorView, $companyModel, $request);
            }
        }
    }
}