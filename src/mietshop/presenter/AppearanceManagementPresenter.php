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
                        // Request or show all colors choosable and such..
                        $this->appearanceManagementView->show($companyData, "update", "color");
                    }
                    else if($request->isQueryParameterMatching("target", "image"))
                    {
                        if($request->isQueryParameterMatching("element", "logo"))
                        {
                            $this->appearanceManagementView->show($companyData, "update", "image", "logo");
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
                    $validElements = [ "color" => [ "frame" => "Farbe für Rahmen", "window" => "Farbe für Fenster", "background" => "Farbe für Hintergrund", "text" => "Text-Farbe", "link" => "Link-Farbe", "interesting" => "Hauptfarbe", "uninteresting" => "Nebenfarbe", "actionExecute" => "Ausführen-Schaltfläche-Farbe", "actionAbort" => "Abbruch-Schaltfläche-Farbe" ], "image" => [ "logo" => "Firmenlogo" ] ];

                    if($request->isPostParameterMatching("target", "color"))
                    {
                        $validColors = [ "primary" => "Color name written fully", "secondary" => "Other color name written fully", "danger" => "Also a color name written fully" ];

                        $validColorElements = $validElements["color"];
                        $validColorElementsKeys = array_keys($validColorElements);

                        foreach($validColorElementsKeys as $validColorElementKey)
                        {
                            $validColorElementName = $validColorElements[$validColorElementKey];

                            if($request->isPostParameterMatching("element", $validColorElementName))
                            {
                                $responseCode = 1; // TODO Use here corresponding model function to update the specific appearance element. Also, use preg_match to verify the color sent (HTML color code hexadecimal - also for Bootstrap colors).

                                $this->appearanceManagementView->show($companyData, "update", "color", $validColorElementName, $responseCode, $validElements, $validColors);

                                return;
                            }
                        }

                        // Expected some valid kind of transmitted color code BUT instead got something invalid or nothing.
                        $this->showPageDoesNotExist($companyData);
                    }
                    else if($request->isPostParameterMatching("target", "image"))
                    {
                        $validImageElements = $validElements["image"];
                        $validImageElementsKeys = array_keys($validImageElements);

                        foreach($validImageElementsKeys as $validImageElementKey)
                        {
                            $validImageElementName = $validImageElements[$validImageElementKey];

                            // New company logo transmitted
                            if($request->isPostParameterMatching("element", $validImageElementName))
                            {
                                $image = $request->getPOSTValue("element");

                                // TODO Process $image, maybe convert it correctly and such...

                                $responseCode = 1; // TODO Use here corresponding model function to update the specific appearance element.

                                $this->appearanceManagementView->show($companyData, "update", "image", $validImageElementName, $responseCode, $validElements);

                                return;
                            }
                        }

                        // Expected a transmitted image as a new company logo BUT instead got nothing.
                        $this->showPageDoesNotExist($companyData);
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
        }
    }
}