<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAccountView;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IAuthPresenter;
use src\mietshop\contracts\ILoginView;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\AccountModel;

class AuthPresenter implements IAuthPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private ILoginView $loginView;
    private IAccountView $accountView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, ILoginView $loginView, IAccountView $accountView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->loginView = $loginView;
        $this->accountView = $accountView;
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

    private function redirectToLogin() : void
    {
        header('Location: /?view=login');
        exit(0);
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();
        $queryViewValue = $httpRequest->getQueryValue("view");

        if(!empty($companyData) && is_string($queryViewValue))
        {
            if(strcmp($queryViewValue, "login") === 0)
            {
                $possiblePOSTEmail = $httpRequest->getPOSTValue("Email");
                $possiblePOSTPassword = $httpRequest->getPOSTValue("Password");

                if(isset($sessionValues["Email"])) // Am I logged in yet?
                {
                    // Then redirect to the account page.
                    $this->redirectToAccount();
                }
                else if(is_string($possiblePOSTEmail) && is_string($possiblePOSTPassword)) // Am I logging in after credentials submitted before?
                {
                    // Then verify credentials and save the login status

                    // Try to find an account in the system, related to that email given.
                    $accountData = $this->accountModel->readAccount($possiblePOSTEmail);

                    if(!empty($accountData)) // Does the account exist?
                    {
                        // Then the account exists

                        if(password_verify($possiblePOSTPassword, $accountData["PasswordHash"])) // Is the password correct?
                        {
                            // Then the password is correct.

                            session_regenerate_id(true);

                            // Remember the logged in user (login status)
                            $sessionValues["Email"] = $possiblePOSTEmail;

                            // Show the account page
                            $this->redirectToAccount();
                        }
                        else
                        {
                            // Then the password is not correct.
                            $this->loginView->show($companyData, "Credentials invalid");
                        }
                    }
                    else
                    {
                        // Then the account does not exist
                        $this->loginView->show($companyData, "Credentials invalid");
                    }
                }
                else if(!isset($sessionValues["Email"])) // Am I not logged in yet?
                {
                    // Then show the login page.
                    $this->loginView->show($companyData, null);
                }
                else
                {
                    $this->errorView->show($companyData, "Ungültiger Login", "Diese Anfrage oder Aktion ist ungültig.");
                }
            }
            else if(strcmp($queryViewValue, "account") === 0)
            {
                if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Am I logged in yet?
                {
                    // Then I am allowed to visit the account page

                    $accountData = $this->accountModel->readAccount($sessionValues["Email"]);

                    if(!empty($accountData)) // Is the account data (still) available? (an admin might have deleted meanwhile the account via DBMS)
                    {
                        $determindRole = "Loading Error";

                        switch($accountData["PermissionLevel"])
                        {
                            case IAccountModel::ROLE_ADMIN:
                                $determindRole = "Admin";
                            break;

                            case IAccountModel::ROLE_OWNER:
                                $determindRole = "Owner";
                            break;

                            case IAccountModel::ROLE_SUPPORTER:
                                $determindRole = "Supporter";
                            break;

                            case IAccountModel::ROLE_MODERATOR:
                                $determindRole = "Moderator";
                            break;

                            case IAccountModel::ROLE_GUEST:
                                $determindRole = "Guest";
                            break;

                            default:
                                $this->errorView->show($companyData, "Integritätsfehler", "Ihre Befugnisse scheinen ungültig zu sein."); // Else there is a bug because more roles do not exist ("Loading Error")
                                exit(0);
                        }

                        $this->accountView->show($companyData, ["Email" => $sessionValues["Email"], "PermissionRole" => $determindRole]);
                    }
                    else
                    {
                        $this->errorView->show($companyData, "Integritätsfehler", "Ihre Anmeldung ist nicht mehr gültig. Bitte melden Sie sich erneut an. Sollte das Problem weiterhin bestehen, wenden Sie sich an den Support."); // For some reason the account does not exist anymore, although you have a verified session access generally to an account (most likely deleted by admin meanwhile).
                        session_destroy();
                    }
                }
                else
                {
                    // Then I am not allowed to see the account page (I need to login)
                    $this->redirectToLogin();
                    exit(0);
                }
            }
            else
            {
                $this->errorView->show($companyData, "Sackgasse", "Dieser Dienst existiert nicht.");
            }
        }
        else
        {
            $this->errorView->show($companyData, "Dienstfehler", "Mindestens ein Dienst ist derzeit intern erreichbar. Versuchen Sie es später erneut.");
        }
    }
}