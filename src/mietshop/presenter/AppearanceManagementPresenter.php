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
        $validBootstrapColors = IAppearanceModel::ACCEPTED_BS_COLORS;
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

                        $possibleOldColorSaved = $this->appearanceModel->getColor($elementValue);

                        $isCustomColor = $this->userInputModel->isValidHexRGBCode($possibleOldColorSaved);

                        // Request or show all colors choosable and such..
                        $this->appearanceManagementView->show($companyData, "update", "Color", $elementValue, 0, $validElements, $validBootstrapColors, $possibleOldColorSaved, $isCustomColor);
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

                        $postElementValue = $request->getPOSTValue("element");
                        $isAcceptedElement = is_string($postElementValue) && in_array($postElementValue, $validElementNames);

                        if(!$isAcceptedElement)
                        {
                            $this->showPageDoesNotExist($companyData);
                        }

                        $oldColor = $this->appearanceModel->getColor($postElementValue);

                        $possibleColorBootstrap = $request->getPOSTValue("colorBootstrap");
                        $possibleColorPicked = $request->getPOSTValue("colorPicked");
                        $possibleColorHexCode = $request->getPOSTValue("colorHexCode");

                        $isOldColorBootstrap = in_array($oldColor, $validBootstrapColors);
                        $isOldColorHexCode = $this->userInputModel->isValidHexRGBCode($oldColor);

                        $areColorFieldsSentTechnically = is_string($possibleColorBootstrap) && is_string($possibleColorPicked) && is_string($possibleColorHexCode);

                        if($areColorFieldsSentTechnically)
                        {
                            // Load previous old color saved (if no custom color gets chosen)
                            $colorChosen = "";
                            $sentColorStatusCode = 0; // Neutral code to show that at this moment all fields seem to be valid (until further processed).
                            $isCustomColor = false;

                            if(empty($possibleColorHexCode)) // Regard Bootstrap color or the color picker?
                            {
                                // Was a custom color picked? (pure black not supported currently - because interpreted as "color not picked").
                                if(strcmp($possibleColorPicked, "#000000") !== 0)
                                {
                                    if($this->userInputModel->isValidHexRGBCode($possibleColorPicked))
                                    {
                                        $colorChosen = $possibleColorPicked;
                                        $isCustomColor = true;
                                    }
                                    else // picked color is for some reason invalid (probably request manipulated)
                                    {
                                        $sentColorStatusCode = -3; // say that the given picked color (hex code) is invalid, so the field gets highlighted correspondingly.
                                    }
                                }
                                // Is it a known pre-defined Bootstrap color? (to prevent manipulation)
                                else if(in_array($possibleColorBootstrap, $validBootstrapColors))
                                {
                                    $colorChosen = $possibleColorBootstrap;
                                }
                                else // Invalid Bootstrap color given (request probably manipulated), so tell "Invalid request".
                                {
                                    $this->showPageDoesNotExist($companyData);
                                    // $sentColorStatusCode = -2; // say that the sent Bootstrap color name is invalid, so the field gets highlighted correspondingly.
                                }
                            }
                            else // Regard the custom hex color code?
                            {
                                if($this->userInputModel->isValidHexRGBCode($possibleColorHexCode))
                                {
                                    $colorChosen = $possibleColorHexCode;
                                    $isCustomColor = true;
                                }
                                else // sent custom hex code is invalid.
                                {
                                    $sentColorStatusCode = -4; // say that the given hex code is invalid, so the field gets highlighted correspondingly.
                                }
                            }

                            // valid color was sent and determined, so further try to save it.
                            if($sentColorStatusCode >= 0)
                            {
                                $sentColorStatusCode = $this->appearanceModel->setColor($colorChosen, $postElementValue) ? 1 : -1;
                            }

                            $this->appearanceManagementView->show($companyData, "update", "Color", $postElementValue, $sentColorStatusCode, $validElements, $validBootstrapColors, $colorChosen, $isCustomColor);

                            return;
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

                                $sentColorStatusCode = 1; // TODO Use here corresponding model function to update the specific appearance element.

                                $this->appearanceManagementView->show($companyData, "update", "Image", $validImageElementName, $sentColorStatusCode, $validElements);

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