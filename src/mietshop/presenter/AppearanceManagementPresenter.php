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
        $this->errorView->show($companyData, "Anfrage ungültig", "Die angeforderte Funktion oder Seite existiert nicht.");
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
                    else if($request->isQueryParameterMatching("target", "image"))
                    {
                        if($request->isQueryParameterMatching("element", "logo"))
                        {
                            $this->appearanceManagementView->show($companyData, "all", "image", "logo");
                        }
                        else
                        {
                            // This feature does not exist / not implemented.
                            $this->showPageDoesNotExist($companyData);
                        }
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
                        if($request->isPostParameterMatching("element", "frame"))
                        {
                            $responseCode = 1; // TODO Use here corresponding model function to update the specific appearance element. Also, use preg_match to verify the color sent (HTML color code hexadecimal - also for Bootstrap colors).

                            

                            $this->appearanceManagementView->show($companyData, "update", "color", "frame", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "window"))
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "window", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "background"))
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "background", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "text"))
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "text", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "link"))
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "link", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "interesting")) // especially for button
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "interesting", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "uninteresting")) // especially for button
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "uninteresting", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "actionExecute")) // especially for button
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "actionExecute", $responseCode);
                        }
                        else if($request->isPostParameterMatching("element", "actionAbort")) // especially for button
                        {
                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "color", "actionAbort", $responseCode);
                        }
                        else
                        {
                            // Expected some valid kind of transmitted color code BUT instead got something invalid or nothing.
                            $this->showPageDoesNotExist($companyData);
                        }
                    }
                    else if($request->isPostParameterMatching("target", "image"))
                    {
                        // New company logo transmitted
                        if($request->isPostParameterMatching("element", "logo"))
                        {
                            $image = $request->getPOSTValue("element");

                            // TODO Process $image, maybe convert it correctly and such...

                            $responseCode = 1;

                            $this->appearanceManagementView->show($companyData, "update", "image", "logo", $responseCode);
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