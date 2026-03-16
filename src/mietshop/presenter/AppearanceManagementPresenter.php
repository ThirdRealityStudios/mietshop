<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAppearanceManagementPresenter;
use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IUserInputModel;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AppearanceManagementPresenter implements IAppearanceManagementPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAppearanceModel $appearanceModel;
    private IUserInputModel $userInputModel;
    private IAppearanceManagementView $appearanceManagementView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAppearanceModel $appearanceModel, IUserInputModel $userInputModel, IAppearanceManagementView $appearanceManagementView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->appearanceModel = $appearanceModel;
        $this->userInputModel = $userInputModel;

        $this->appearanceManagementView = $appearanceManagementView;
        $this->errorView = $errorView;
    }

    private function showPageDoesNotExist(array $companyData)
    {
        $this->errorView->show($companyData, "Anfrage ungültig", "Die angeforderte Funktion oder Seite existiert nicht.");
        exit(0);
    }

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        $validColors = IAppearanceModel::ACCEPTED_COLORS;
        $validElements = IAppearanceModel::ACCEPTED_ELEMENTS;

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
                    if($request->isQueryParameterMatching("target", "Color"))
                    {
                        $elementValue = $request->getQueryValue("element") ?? "";

                        // Request or show all colors choosable and such..
                        $this->appearanceManagementView->show($companyData, "update", "Color", $elementValue, 0, $validElements, $validColors);
                    }
                    else if($request->isQueryParameterMatching("target", "Image"))
                    {
                        if($request->isQueryParameterMatching("element", "Logo"))
                        {
                            $this->appearanceManagementView->show($companyData, "update", "Image", "Logo", 0, $validElements);
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
                    if($request->isPostParameterMatching("target", "Color"))
                    {
                        $validElementNames = array_keys($validElements["Color"]);

                        $possibleColorBootstrap = $request->getPOSTValue("colorBootstrap");
                        $possibleColorPicked = $request->getPOSTValue("colorPicked");
                        $possibleColorHexCode = $request->getPOSTValue("colorHexCode");

                        $areColorFieldsSentTechnically = is_string($possibleColorBootstrap) && is_string($possibleColorPicked) && is_string($possibleColorHexCode);

                        /*
                        var_dump($possibleColorBootstrap);
                        var_dump($possibleColorPicked);
                        var_dump($possibleColorHexCode);
                        die();
                        */

                        if($areColorFieldsSentTechnically)
                        {
                            $colorChosen = "";

                            // Was a custom hex code sent?
                            if($this->userInputModel->isValidHexRGBCode($possibleColorHexCode))
                            {
                                $colorChosen = $possibleColorHexCode;
                            }
                            // Was a valid color transmitted by the color picker (black #000000 currently not supported and interpreted as "not sent" or "not chosen" - because it is easier currently)
                            else if(strcmp($possibleColorPicked, "#000000") !== 0 && $this->userInputModel->isValidHexRGBCode($possibleColorPicked))
                            {
                                $colorChosen = $possibleColorPicked;
                            }
                            // Is it a known pre-defined Bootstrap color? (to prevent manipulation)
                            else if(in_array($possibleColorBootstrap, $validColors))
                            {
                                $colorChosen = $possibleColorBootstrap;
                            }
                            else
                            {
                                // Something went wrong. Invalid request.
                                $this->showPageDoesNotExist($companyData);
                            }

                            foreach($validElementNames as $validElementName)
                            {
                                if($request->isPostParameterMatching("element", $validElementName))
                                {
                                    $updateResponse = $this->appearanceModel->setColor($colorChosen, $validElementName);

                                    $this->appearanceManagementView->show($companyData, "update", "Color", $validElementName, $updateResponse ? 1 : -1, $validElements, $validColors);

                                    return;
                                }
                            }
                        }
                        else
                        {
                            // Expected some valid kind of transmitted color code BUT instead got something invalid or nothing.
                            $this->showPageDoesNotExist($companyData);
                        }
                    }
                    else if($request->isPostParameterMatching("target", "Image"))
                    {
                        $validImageElements = $validElements["Image"];
                        $validImageElementsKeys = array_keys($validImageElements);

                        foreach($validImageElementsKeys as $validImageElementKey)
                        {
                            $validImageElementName = $validImageElements[$validImageElementKey];

                            // New company logo transmitted
                            if($request->isPostParameterMatching("element", $validImageElementName))
                            {
                                $image = $request->getPOSTValue("element");

                                // TODO Process $image, maybe convert it correctly and such...

                                $updateResponse = 1; // TODO Use here corresponding model function to update the specific appearance element.

                                $this->appearanceManagementView->show($companyData, "update", "Image", $validImageElementName, $updateResponse, $validElements);

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