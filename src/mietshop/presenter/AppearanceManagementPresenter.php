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

    private function showPageDoesNotExist(array $companyData)
    {
        $this->errorView->show($companyData, "Anfrage ungültig", "Die angeforderte Funktion or Seite existiert nicht.");
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

                if($request->isQueryParameterMatching("action", "all"))
                {
                    $this->appearanceManagementView->show($companyData, "all");
                }
                else if($request->isQueryParameterMatching("action", "update"))
                {
                    if($request->isQueryParameterMatching("target", "color"))
                    {
                        $this->appearanceManagementView->show($companyData, "all", "color");
                    }
                    else if($request->isQueryParameterMatching("target", "logo"))
                    {
                        $this->appearanceManagementView->show($companyData, "all", "logo");
                    }
                    else
                    {
                        // This feature does not exist / not implemented.
                        $this->showPageDoesNotExist($companyData);
                    }
                }
                else if($request->isPostParameterMatching("action", "update"))
                {
                    if($request->isPostParameterMatching("target", "color"))
                    {
                        if($request->isPostParameterExisting("colorFrame"))
                        {
                            $responseCode = 1; // TODO Use here corresponding model function to update the specific appearance element. Also, use preg_match to verify the color sent (HTML color code hexadecimal - also for Bootstrap colors).
                        }

                        if($request->isPostParameterExisting("colorWindow"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorBackground"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorText"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorLink"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorButtonInteresting"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorButtonUninteresting"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorButtonExecuteAction"))
                        {

                        }
                        else if($request->isPostParameterExisting("colorButtonAbortAction"))
                        {

                        }
                        else
                        {
                            // Expected some valid kind of transmitted color code BUT instead got something invalid or nothing.
                            $this->showPageDoesNotExist($companyData);
                        }
                    }
                    else if($request->isPostParameterMatching("target", "logo"))
                    {
                        $postImageValue = $request->getPOSTValue("image"); // New company logo transmitted (TODO process still and maybe convert to suitable image format)

                        if(!is_string($postImageValue))
                        {

                        }
                        else
                        {
                            // Expected a transmitted image as a new company logo BUT instead got nothing.
                            $this->showPageDoesNotExist($companyData);
                        }
                    }
                    else
                    {
                        // This update feature does not exist for the desired appearance element.
                        $this->showPageDoesNotExist($companyData);
                    }
                }
                else
                {
                    // This feature does not exist / not implemented.
                    $this->showPageDoesNotExist($companyData);
                }
            }
            else
            {
                // Account does not exist or access denied (missing permissions)
                $this->showPageDoesNotExist([]);
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