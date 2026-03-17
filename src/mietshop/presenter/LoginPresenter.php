<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\ILoginPresenter;
use src\mietshop\contracts\ILoginView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class LoginPresenter implements ILoginPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAppearanceModel $appearanceModel;

    private ILoginView $loginView;
    private IAccountView $accountView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAppearanceModel $appearanceModel, IAccountModel $accountModel, ILoginView $loginView, IAccountView $accountView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->appearanceModel = $appearanceModel;
        $this->accountModel = $accountModel;

        $this->loginView = $loginView;
        $this->accountView = $accountView;
        $this->errorView = $errorView;
    }

    private function redirectToAccount() : void
    {
        header('Location: /?view=account');
        exit(0);
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();
        $appearanceData = $this->appearanceModel->getAppearanceData();
        $queryViewValue = $httpRequest->getQueryValue("view");

        if(!empty($companyData) && is_string($queryViewValue) && strcmp($queryViewValue, "login") === 0)
        {
            $possiblePOSTEmail = $httpRequest->getPOSTValue("Email");
            $possiblePOSTPassword = $httpRequest->getPOSTValue("Password");

            if(isset($sessionValues["Email"])) // Am I logged in yet?
            {
                // Then redirect to the account page,
                // so it does further processing.
                $this->redirectToAccount();
            }
            else if(is_string($possiblePOSTEmail) && is_string($possiblePOSTPassword)) // Am I logging in after credentials submitted before?
            {
                $sentEmailLowerCase = strtolower($possiblePOSTEmail);

                // Then verify credentials and save the login status

                // Try to find an account in the system, related to that email given.
                $accountData = $this->accountModel->readAccount($sentEmailLowerCase);

                // Does the account exist and is the password correct?
                if(!empty($accountData) && password_verify($possiblePOSTPassword, $accountData["PasswordHash"])) // Is the password correct?
                {
                    // Then the password is correct.

                    session_regenerate_id(true);

                    // Remember the logged in user (login status)
                    $sessionValues["Email"] = $sentEmailLowerCase;

                    // Show the account page
                    $this->redirectToAccount();
                }
                else
                {
                    // Then the password is not correct.
                    $this->loginView->show($companyData, $appearanceData, "Anmeldedaten ungültig");
                }
            }
            else // Then I am not valid logged in yet.
            {
                // Then show the login page.
                $this->loginView->show($companyData, $appearanceData, null);
            }
        }
        else
        {
            $this->errorView->show($companyData, $appearanceData, "Ungültige Anfrage", "Der angeforderte Dienst ist nicht verfügbar.");
        }
    }
}