<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAppearanceManagementPresenter;
use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IImageModel;
use src\mietshop\contracts\IValidationModel;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AppearanceManagementPresenter implements IAppearanceManagementPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAppearanceModel $appearanceModel;
    private IImageModel $imageModel;
    private IValidationModel $userInputModel;
    private IAppearanceManagementView $appearanceManagementView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAppearanceModel $appearanceModel, IImageModel $imageModel, IValidationModel $userInputModel, IAppearanceManagementView $appearanceManagementView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->appearanceModel = $appearanceModel;
        $this->imageModel = $imageModel;
        $this->userInputModel = $userInputModel;

        $this->appearanceManagementView = $appearanceManagementView;
        $this->errorView = $errorView;
    }

    private function showInvalidRequestView(array $companyData)
    {
        $this->errorView->show($companyData, $this->appearanceModel->getAppearanceData(), "Anfrage ungültig", "Die angeforderte Funktion oder Seite existiert nicht.");
    }

    private function showIntegrityErrorView(array $companyData)
    {
        $this->errorView->show($companyData, $this->appearanceModel->getAppearanceData(), "Datenintegritätsfehler", "Die Seite kann aufgrund eines Integritätsfehlers nicht geladen werden. Bitte versuchen Sie es später erneut oder konsultieren Sie einen Administrator.");
    }

    private function postProcessColorUpdate(IHTTPRequest $request, array $companyData, array $validElements) : void
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

                            if(empty($possibleColorHexCode))
                            {
                                // Just choose the color picker hex code.
                                // If it was manipulated, setColor(..) will fail anyway (so it is safe).
                                $colorChosen = $possibleColorPicked;
                            }
                            else
                            {
                                // Custom color code has higher priority than color picker.
                                $colorChosen = $possibleColorHexCode;
                            }

                            $updatedColorSuccessfully = $this->appearanceModel->setColor($colorChosen, $postElementValue);

                            // Reload the appearance data, as it was updated.
                            $appearanceData = $this->appearanceModel->getAppearanceData();

                            if($updatedColorSuccessfully)
                            {
                                $this->appearanceManagementView->showColorUpdateSuccessful($companyData, $appearanceData, $postElementValue, $postCustomValue);
                            }
                            else
                            {
                                $this->appearanceManagementView->showColorUpdateFailed($companyData, $appearanceData, $postElementValue, $postCustomValue);
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

                            // Reload the appearance data, as it was updated.
                            $appearanceData = $this->appearanceModel->getAppearanceData();

                            if($updatedColorSuccessfully)
                            {
                                $this->appearanceManagementView->showColorUpdateSuccessful($companyData, $appearanceData, $postElementValue, $postCustomValue);
                            }
                            else
                            {
                                $this->appearanceManagementView->showColorUpdateFailed($companyData, $appearanceData, $postElementValue, $postCustomValue);
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

    private function queryProcessColorUpdate(IHTTPRequest $request, array $companyData, array $appearanceData, array $validElements, array $validBootstrapColors) : void
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

                        $this->appearanceManagementView->showCustomColorForm($companyData, $appearanceData, $elementValue, $preparedHexCodeColor);
                    }
                    else if(strcmp($customValue, "no") === 0)
                    {
                        // Then load the default color form

                        $isBootstrapColor = in_array($savedColor, $validBootstrapColors);
                        $preparedBootstrapColor = $isBootstrapColor ? $savedColor : $validBootstrapColors[0];

                        $this->appearanceManagementView->showDefaultColorForm($companyData, $appearanceData, $elementValue, $validBootstrapColors, $preparedBootstrapColor);
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

            $this->appearanceManagementView->showAllColorElements($companyData, $appearanceData, $validColorElements);
        }
    }

    private function queryProcessImageUpdate(IHTTPRequest $request, array $companyData, array $appearanceData, array $validElements) : void
    {
        $elementValue = $request->getQueryValue("element");

        $validImageElements = $validElements["Image"];

        if(is_string($elementValue))
        {
            // Then show the image form for the given element

            $isValidImageElementSent = in_array($elementValue, array_keys($validImageElements));

            if($isValidImageElementSent)
            {
                // Then a valid image element was given via GET

                if(strcmp($elementValue, "Logo") === 0)
                {
                    $this->appearanceManagementView->showImageLogoForm($companyData, $appearanceData, $elementValue);
                }
                else if(strcmp($elementValue, "Background") === 0)
                {

                }
                else
                {
                    $this->showInvalidRequestView($companyData);
                }
            }
            else
            {
                $this->showInvalidRequestView($companyData);
            }
        }
        else
        {
            // Then show all elements which are possible to change the color of.

            $this->appearanceManagementView->showAllImageElements($companyData, $appearanceData, $validImageElements);
        }
    }

    private function postProcessImageUpdate(IHTTPRequest $request, array $companyData, array $validElements) : void
    {
        $postElementValue = $request->getPOSTValue("element");

        if(is_string($postElementValue))
        {
            // Then a specific element wants to be updated with a new color

            $validImageElementNames = array_keys($validElements["Image"]);
            $isAcceptedElement = in_array($postElementValue, $validImageElementNames);

            if($isAcceptedElement)
            {
                if(strcmp($postElementValue, "Logo") === 0)
                {
                    $updatedImageSuccessfully = $this->imageModel->upload(DIRECTORY_SEPARATOR, "favicon.png", $_FILES, "image");

                    // Reload the appearance data, as it was updated.
                    $appearanceData = $this->appearanceModel->getAppearanceData();

                    if($updatedImageSuccessfully)
                    {
                        $this->appearanceManagementView->showImageUpdateSuccessful($companyData, $appearanceData, $postElementValue);
                    }
                    else
                    {
                        $this->appearanceManagementView->showImageUpdateFailed($companyData, $appearanceData, $postElementValue);
                    }
                }
                else if(strcmp($postElementValue, "Background") === 0)
                {

                }
                else
                {
                    $this->showInvalidRequestView($companyData);
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

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        $validBootstrapColors = IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS;
        $validElements = IAppearanceModel::ACCEPTED_ELEMENTS;

        $appearanceData = $this->appearanceModel->getAppearanceData();

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
                    $this->appearanceManagementView->showOptions($companyData, $appearanceData);
                }
                else if($request->isQueryParameterMatching("action", "update"))
                {
                    if($request->isQueryParameterMatching("target", "Color"))
                    {
                        $this->queryProcessColorUpdate($request, $companyData, $appearanceData, $validElements, $validBootstrapColors);
                    }
                    else if($request->isQueryParameterMatching("target", "Image"))
                    {
                        $this->queryProcessImageUpdate($request, $companyData, $appearanceData, $validElements);
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
                        $this->postProcessColorUpdate($request, $companyData, $validElements);
                    }
                    else if($request->isPostParameterMatching("target", "Image"))
                    {
                        $this->postProcessImageUpdate($request, $companyData, $validElements);
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