<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\database\IDatabaseAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\AccountModel;
use src\mietshop\model\AppearanceModel;
use src\mietshop\model\CategoryModel;
use src\mietshop\model\CompanyModel;
use src\mietshop\model\TextFormatView;
use src\mietshop\model\UserInputModel;
use src\mietshop\presenter\AccountManagementPresenter;
use src\mietshop\presenter\AccountPresenter;
use src\mietshop\presenter\AppearanceManagementPresenter;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\presenter\ImprintPresenter;
use src\mietshop\presenter\LoginPresenter;
use src\mietshop\presenter\PrivacyPolicyPresenter;
use src\mietshop\presenter\TermsOfServicePresenter;
use src\mietshop\view\AccountManagementView;
use src\mietshop\view\AccountView;
use src\mietshop\view\AppearanceManagementView;
use src\mietshop\view\ErrorView;
use src\mietshop\view\GlobalView;
use src\mietshop\view\HomeView;
use src\mietshop\view\ImprintView;
use src\mietshop\view\LegalView;
use src\mietshop\view\LoginView;

class Router implements IRouter
{
    private function showPageNotFoundError(IErrorView $errorView, array $companyData, array $appearanceData) : void
    {
        $errorView->show($companyData, $appearanceData, "Seite existiert nicht", "Entweder haben Sie sich vertippt oder derzeit gibt es technische Probleme. Bitte versuchen Sie es später erneut.");
    }

    public function route(IHTTPRequest $request, array &$sessionValues, IDatabaseAdapter $databaseAdapter) : void
    {
        $userInputModel = new UserInputModel();
        $appearanceModel = new AppearanceModel($userInputModel, $databaseAdapter);
        $companyModel = new CompanyModel($userInputModel, $databaseAdapter);
        $categoryModel = new CategoryModel($databaseAdapter);
        $globalView = new GlobalView();
        $errorView = new ErrorView($globalView);
        $textFormatView = new TextFormatView();

        if(empty($request->getQueryKeys()) && empty($request->getPOSTKeys()))
        {
            $homeView = new HomeView($globalView);
            $homePresenter = new HomePresenter($companyModel, $appearanceModel, $categoryModel, $homeView, $errorView);
            $homePresenter->process($request);
        }
        else
        {
            $loginView = new LoginView($globalView);
            $accountView = new AccountView($globalView);
            $legalView = new LegalView($globalView, $errorView, $textFormatView);
            $accountManagementView = new AccountManagementView($globalView);
            $appearanceManagementView = new AppearanceManagementView($globalView, $errorView);

            $accountModel = new AccountModel($userInputModel, $databaseAdapter);

            $loginPresenter = new LoginPresenter($companyModel, $appearanceModel, $accountModel, $loginView, $accountView, $errorView);
            $accountPresenter = new AccountPresenter($companyModel, $appearanceModel, $accountModel, $accountView, $errorView);
            $accountManagementPresenter = new AccountManagementPresenter($companyModel, $accountModel, $appearanceModel, $accountManagementView, $errorView);
            $appearanceManagementPresenter = new AppearanceManagementPresenter($companyModel, $accountModel, $appearanceModel, $userInputModel, $appearanceManagementView, $errorView);

            if($request->isParameterMatching("view", "imprint"))
            {
                $imprintView = new ImprintView($globalView);
                $imprintPresenter = new ImprintPresenter($errorView, $imprintView, $companyModel, $appearanceModel, $accountModel);
                $imprintPresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "termsofservice"))
            {
                $termsOfServicePresenter = new TermsOfServicePresenter($legalView, $errorView, $companyModel, $appearanceModel, $accountModel);
                $termsOfServicePresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "privacypolicy"))
            {
                $privacyPolicyPresenter = new PrivacyPolicyPresenter($legalView, $errorView, $companyModel, $appearanceModel, $accountModel);
                $privacyPolicyPresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "login"))
            {
                // Show the login page or authenticate and then redirect (if the user is yet logged in)
                $loginPresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "account"))
            {
                // Show the account page (will check if you are authorized or logged in yet)
                $accountPresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "accounts"))
            {
                // Show the account management page
                $accountManagementPresenter->process($request, $sessionValues);
            }
            else if($request->isParameterMatching("view", "appearance"))
            {
                $appearanceManagementPresenter->process($request, $sessionValues);
            }
            else
            {
                $this->showPageNotFoundError($errorView, $companyModel->getCompanyData(), $appearanceModel->getAppearanceData());
            }
        }
    }
}