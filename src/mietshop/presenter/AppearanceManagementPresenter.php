<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAppearanceManagementPresenter;
use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AppearanceManagementPresenter implements IAppearanceManagementPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAppearanceManagementView $appearanceManagementView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAppearanceManagementView $appearanceManagementView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->appearanceManagementView = $appearanceManagementView;
        $this->errorView = $errorView;
    }

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        if(isset($sessionValues["Email"]) && is_string($sessionValues["Email"])) // Is the visitor logged in?
        {
            $accountFetchData = $this->accountModel->readAccount($sessionValues["Email"]);

            // But does the account still exist? Maybe the session is older and the account does not exist anymore (e.g. admin deleted it or owner).
            // Also check (if the account exists) if the user may manage accounts to give access.
            if(!empty($accountFetchData) && $this->accountModel->isSupervisor($sessionValues["Email"]))
            {
                $companyData = $this->companyModel->getCompanyData();

                if($request->isParameterMatching("action", "all"))
                {
                    $this->appearanceManagementView->show($companyData, "all");
                }
                else if($request->isParameterMatching("action", "update"))
                {
                    if($request->isParameterMatching("target", "color"))
                    {
                        $postColorValue = $request->getPOSTValue("color"); // HTML color code (TODO to verify still)


                    }
                    else if($request->isParameterMatching("target", "logo"))
                    {
                        $postLogoValue = $request->getPOSTValue("companyLogo"); // New company logo transmitted (TODO process still and maybe convert to suitable image format)

                        if(!empty($postLogoValue))
                        {

                        }
                        else
                        {
                            $this->appearanceManagementView->show($companyData, $actionUpdateBasicColors, $responseCode);
                        }
                    }
                    else
                    {
                        // This feature does not exist / not implemented.
                        $this->errorView->show($companyData, "Angeforderte Funktion ungültig", "Die angeforderte Funktion existiert nicht.");
                    }

                    // Does the user (supervisor) want to update the overall basic colors used for all views? (= a POST action)
                    if(is_string($postColorValue))
                    {
                        $responseCode = 1; // TODO Use here corresponding model function to update the specific appearance element. Also, use preg_match to verify the color sent (HTML color code hexadecimal - also for Bootstrap colors).

                        $this->appearanceManagementView->show($companyData, $actionUpdateBasicColors, $responseCode);
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
                $this->errorView->show([], "Fehler", "Zugriff verweigert");
            }
        }
        else
        {
            // User not logged in
            header("Location: /?view=login");
            exit(0);
        }
    }
}