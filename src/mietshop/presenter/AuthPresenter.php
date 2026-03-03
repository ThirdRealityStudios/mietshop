<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IAuthPresenter;
use src\mietshop\contracts\ILoginView;
use src\mietshop\infrastructure\http\HTTPRequest;

class AuthPresenter implements IAuthPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private ILoginView $loginView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, ILoginView $loginView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->loginView = $loginView;
        $this->errorView = $errorView;
    }

    private function showErrorServiceNotAvailable() : void
    {
        $this->errorView->show([], "Dienst nicht erreichbar", "Bitte versuchen Sie es später erneut.");
    }

    private function redirectToAccount() : void
    {
        header('Location: /?view=account');
        exit(0);
    }

    public function process(HTTPRequest $httpRequest, array $sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();

        $postEmail = $httpRequest->getPOSTValue("Email");
        $postPassword = $httpRequest->getPOSTValue("Password");

        $isLoggedIn = isset($sessionValues["Email"]);
        $isLoggingIn = is_string($postEmail) && is_string($postPassword);
        $isNotLoggedIn = !isset($sessionValues["Email"]);

        if(!empty($companyData))
        {
            if($isLoggedIn)
            {
                $this->redirectToAccount();
            }
            else if($isLoggingIn)
            {
                // Verify credentials and if so, start session
                $accountData = $this->accountModel->readAccount($postEmail);

                if(!empty($accountData))
                {
                    if(password_verify($postPassword, $accountData["PasswordHash"]))
                    {
                        // Login could be verified successfully

                        // Remember the logged in user now:
                        $_SESSION["Email"] = $postEmail;

                        $this->redirectToAccount();
                    }
                    else
                    {
                        $this->showErrorServiceNotAvailable();
                    }
                }
                else
                {
                    $this->showErrorServiceNotAvailable();
                }
            }
            else if($isNotLoggedIn)
            {
                $this->loginView->show($companyData);
            }
            else
            {
                $this->showErrorServiceNotAvailable();
            }
        }
        else
        {
            $this->showErrorServiceNotAvailable();
        }
    }
}