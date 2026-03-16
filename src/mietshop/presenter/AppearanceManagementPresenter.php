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

    private function showInvalidRequestView(array $companyData)
    {
        $this->errorView->show($companyData, "Anfrage ungültig", "Die angeforderte Funktion oder Seite existiert nicht.");
    }

    private function showIntegrityErrorView(array $companyData)
    {
        $this->errorView->show($companyData, "Datenintegritätsfehler", "Die Seite kann aufgrund eines Integritätsfehlers nicht geladen werden. Bitte versuchen Sie es später erneut oder konsultieren Sie einen Administrator.");
    }

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        $validBootstrapColors = IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS;
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
                    $this->appearanceManagementView->showOptions($companyData);
                }
                else if($request->isQueryParameterMatching("action", "update"))
                {
                    if($request->isQueryParameterMatching("target", "Color"))
                    {
                        $elementValue = $request->getQueryValue("element");

                        $validColorElements = $validElements["Color"];

                        if(is_string($elementValue))
                        {
                            // Then show the color form for the given element

                            $isValidColorElementSent = in_array($elementValue, array_keys($validColorElements));

                            if($isValidColorElementSent)
                            {
                                // Then a valid color element was given via GET

                                $savedColor = $this->appearanceModel->getColor($elementValue);

                                // Could the color be loaded successfully?
                                if(empty($savedColor))
                                {
                                    $this->showIntegrityErrorView($companyData);

                                    return;
                                }

                                $customValue = $request->getQueryValue("custom");

                                if(is_string($customValue))
                                {
                                    // Then see if user-custom or default colors are wanted to see

                                    if(strcmp($customValue, "yes") === 0)
                                    {
                                        // Then load the form for user-custom colors

                                        $isHexCodeColor = $this->userInputModel->isValidHexRGBCode($savedColor);
                                        $preparedHexCodeColor = $isHexCodeColor ? $savedColor : "";

                                        $this->appearanceManagementView->showCustomColorForm($companyData, $elementValue, $preparedHexCodeColor);
                                    }
                                    else if(strcmp($customValue, "no") === 0)
                                    {
                                        // Then load the default color form

                                        $isBootstrapColor = in_array($savedColor, $validBootstrapColors);
                                        $preparedBootstrapColor = $isBootstrapColor ? $savedColor : $validBootstrapColors[0];

                                        $this->appearanceManagementView->showDefaultColorForm($companyData, $elementValue, $validBootstrapColors, $preparedBootstrapColor);
                                    }
                                    else
                                    {
                                        $this->showInvalidRequestView($companyData);

                                        return;
                                    }
                                }
                                else
                                {
                                    $this->showInvalidRequestView($companyData);

                                    return;
                                }
                            }
                            else
                            {
                                $this->showInvalidRequestView($companyData);

                                return;
                            }
                        }
                        else
                        {
                            // Then show all elements which are possible to change the color of.

                            $this->appearanceManagementView->showAllColorElements($companyData, $validColorElements);
                        }
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
                            $this->showInvalidRequestView($companyData);

                            return;
                        }
                    }
                    else
                    {
                        // This feature does not exist / not implemented.
                        $this->showInvalidRequestView($companyData);

                        return;
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
                            $this->showInvalidRequestView($companyData);

                            return;
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
                            $messageValue = 0; // No problems

                            if(empty($possibleColorHexCode))
                            {
                                // No custom hex color code was provided.

                                if(strcmp($possibleColorPicked, "#000000") === 0)
                                {
                                    // Then no color was picked or no hex code was provided.

                                    if(empty($possibleColorBootstrap))
                                    {
                                        // Also no Bootstrap color was provided (likely a form generation bug or manipulated request)

                                        $this->showInvalidRequestView($companyData);

                                        return;
                                    }
                                    else
                                    {
                                        // Nothing was provided but a Bootstrap color.

                                        if(in_array($possibleColorBootstrap, $validBootstrapColors))
                                        {
                                            // The Bootstrap color is also valid.

                                            $colorChosen = $possibleColorBootstrap;
                                        }
                                        else
                                        {
                                            // But it is not valid (so likely manipulated request).

                                            $this->showInvalidRequestView($companyData);

                                            return;
                                        }
                                    }
                                }
                                else
                                {
                                    // Then a color was picked.

                                    if($this->userInputModel->isValidHexRGBCode($possibleColorPicked))
                                    {
                                        // It is also valid.

                                        $colorChosen = $possibleColorPicked;
                                    }
                                    else
                                    {
                                        // But the request was likely manipulated.

                                        $this->showInvalidRequestView($companyData);

                                        return;
                                    }
                                }
                            }
                            else
                            {
                                // A custom hex color code was provided.

                                if($this->userInputModel->isValidHexRGBCode($possibleColorHexCode))
                                {
                                    // It is in a valid format.

                                    $colorChosen = $possibleColorHexCode;
                                }
                                else
                                {
                                    // It is not valid though (likely user issue - but not a manipulated request mostly).

                                    $messageValue = -2; // Mark the custom hex color code field as invalid.
                                }
                            }

                            // Try to save it.
                            if($messageValue >= 0)
                            {
                                $messageValue = $this->appearanceModel->setColor($colorChosen, $postElementValue) ? 1 : -1;
                            }

                            $isColorPickedOrHexadecimal = $this->userInputModel->isValidHexRGBCode($colorChosen);

                            $this->appearanceManagementView->show($companyData, "update", "Color", $postElementValue, $messageValue, $validElements, $validBootstrapColors, $colorChosen, $isColorPickedOrHexadecimal);

                            return;
                        }
                        else
                        {
                            // Expected some valid kind of transmitted color code BUT instead got something invalid or nothing.
                            $this->showInvalidRequestView($companyData);

                            return;
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

                                $messageValue = 1; // TODO Use here corresponding model function to update the specific appearance element.

                                $this->appearanceManagementView->show($companyData, "update", "Image", $validImageElementName, $messageValue, $validElements);

                                return;
                            }
                        }

                        // Expected a transmitted image as a new company logo BUT instead got nothing.
                        $this->showInvalidRequestView($companyData);

                        return;
                    }
                    else
                    {
                        // This update feature does not exist for the desired appearance element.
                        $this->showInvalidRequestView($companyData);

                        return;
                    }
                }
                else
                {
                    // This feature does not exist / not implemented.
                    $this->showInvalidRequestView($companyData);

                    return;
                }
            }
            else
            {
                // Account does not exist or access denied (missing permissions)
                $this->showInvalidRequestView([]);

                return;
            }
        }
        else
        {
            // User not logged in
            header("Location: /?view=login");
        }
    }
}