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
                            // This feature does not exist / not implemented.
                            $this->showInvalidRequestView($companyData);

                            return;
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
                        $postElementValue = $request->getPOSTValue("element");

                        if(is_string($postElementValue))
                        {
                            // Then a specific element wants to be updated with a new color

                            $validColorElementNames = array_keys($validElements["Color"]);
                            $isAcceptedElement = in_array($postElementValue, $validColorElementNames);

                            if($isAcceptedElement)
                            {
                                // Then the given color element is accepted

                                $postCustomValue = $request->getPOSTValue("custom");

                                if(is_string($postCustomValue))
                                {
                                    if(strcmp($postCustomValue, "yes") === 0)
                                    {
                                        // Then a user-custom color is expected to be sent (format always RGB(A) hex code)

                                        $possibleColorPicked = $request->getPOSTValue("colorPicked");
                                        $possibleColorHexCode = $request->getPOSTValue("colorHexCode");

                                        $areCustomColorsSent = is_string($possibleColorPicked) && is_string($possibleColorHexCode);

                                        if($areCustomColorsSent)
                                        {
                                            $colorChosen = "";

                                            if($this->userInputModel->isValidHexRGBCode($possibleColorHexCode))
                                            {
                                                // Custom color code has higher priority than color picker.
                                                $colorChosen = $possibleColorHexCode;
                                            }
                                            else
                                            {
                                                // Otherwise just choose the color picker hex code.
                                                // If it was manipulated, setColor(..) will fail anyway (so it is safe).
                                                $colorChosen = $possibleColorPicked;
                                            }

                                            $updatedColorSuccessfully = $this->appearanceModel->setColor($colorChosen, $postElementValue);

                                            if($updatedColorSuccessfully)
                                            {
                                                $this->appearanceManagementView->showColorUpdateSuccessful($companyData, $postElementValue, $postCustomValue);
                                            }
                                            else
                                            {
                                                $this->appearanceManagementView->showColorUpdateFailed($companyData, $postElementValue, $postCustomValue);
                                            }
                                        }
                                        else
                                        {
                                            // Expected hex code color values sent from both,
                                            // color picker and custom hex code field,
                                            // but got nothing (should never happen)

                                            $this->showInvalidRequestView($companyData);
                                        }
                                    }
                                    else if(strcmp($postCustomValue, "no") === 0)
                                    {
                                        // Then a pre-defined Bootstrap color is expected to be sent

                                        $possibleColorBootstrap = $request->getPOSTValue("colorBootstrap");

                                        if(is_string($possibleColorBootstrap))
                                        {
                                            $updatedColorSuccessfully = $this->appearanceModel->setColor($possibleColorBootstrap, $postElementValue);

                                            if($updatedColorSuccessfully)
                                            {
                                                $this->appearanceManagementView->showColorUpdateSuccessful($companyData, $postElementValue, $postCustomValue);
                                            }
                                            else
                                            {
                                                $this->appearanceManagementView->showColorUpdateFailed($companyData, $postElementValue, $postCustomValue);
                                            }
                                        }
                                        else
                                        {
                                            // The expected Bootstrap color was not sent correctly.

                                            $this->showInvalidRequestView($companyData);
                                        }
                                    }
                                    else
                                    {
                                        // Then the desired color mode is not supported / or manipulated request.

                                        $this->showInvalidRequestView($companyData);
                                    }
                                }
                                else
                                {
                                    // Then it is an invalid request (user-custom color desired or default Boostrap presets?)

                                    $this->showInvalidRequestView($companyData);

                                    return;
                                }
                            }
                            else
                            {
                                // Then it is a manipulated request

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

                                // $this->appearanceManagementView->show($companyData, "update", "Image", $validImageElementName, $messageValue, $validElements);

                                // Not implemented yet.
                                $this->showInvalidRequestView($companyData);

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