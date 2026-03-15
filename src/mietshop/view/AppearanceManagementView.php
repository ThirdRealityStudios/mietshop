<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;

class AppearanceManagementView implements IAppearanceManagementView
{
    private IGlobalView $globalView;
    private IErrorView $errorView;

    public function __construct(IGlobalView $globalView, IErrorView $errorView)
    {
        $this->globalView = $globalView;
        $this->errorView = $errorView;
    }

    private function showInvalidRequest(array $companyData) : void
    {
        $this->errorView->show($companyData, "Ungültige Anfrage", "Aktualisierung fehlgeschlagen.");
        exit(0);
    }

    private function genButton(string $urlTargetValue, string $urlElementValue, string $title) : string
    {
        return
        "
        <div class=\"row mb-3\">
            <div class=\"col-12\">
                <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=$urlTargetValue&element=$urlElementValue\">$title</a>
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButton(string $bsColorClass, string $labelText, string $id, string $postValue) : string
    {
        return
        "
        <div class=\"row p-0 mb-3 $bsColorClass rounded align-items-center\">
            <div class=\"col-1 ps-0 h-100 d-inline\">
                <input type=\"radio\" id=\"$id\" name=\"color\" class=\"form-check-input ms-3 d-inline-block\" value=\"$postValue\">
            </div>
            <div class=\"col-11 pe-0 h-100 d-inline\">
                <label class=\"form-check-label fw-bold w-100 p-2 d-inline-block\" for=\"$id\"><span class=\"fs-6\" style=\"filter: invert(100%);\">$labelText</span></label>
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButtons(array $validColors) : string
    {
        $colorRadioButtons = "";

        $validColorsKeys = array_keys($validColors);

        foreach($validColorsKeys as $validBootstrapColor)
        {
            $bsBgColorClass = "bg-".$validBootstrapColor;

            $colorRadioButtons .= $this->genFormColorRadioButton($bsBgColorClass, $validColors[$validBootstrapColor], $bsBgColorClass, $bsBgColorClass);
        }

        return $colorRadioButtons;
    }

    private function genColorForm(string $target, string $element, array $validColors) : string
    {
        $colorRadioButtonsHTML = $this->genFormColorRadioButtons($validColors);

        // Want to see a specific form for an element to change the color for?
        if(strcmp($target, "color") === 0 && !empty($element))
        {
            return
            "
            <div class=\"container-fluid\">
                <div class=\"row\">
                    <div class=\"col-12 p-0\">
                        <p class=\"fs-2 fw-bold\">Bearbeiten</p>
                    </div>
                </div>
                <form class=\"row form\" action=\"/?view=appearance&action=update&target=$target&element=$element\" method=\"post\">
                    <div class=\"col-12\">
                        <div class=\"container-fluid p-0\">
                            $colorRadioButtonsHTML
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <hr class=\"w-100 border border-1 border-primary\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0 w-100\">
                                    <label class=\"form-label fw-bold w-100 fs-6\" for=\"custom\">Eigene Farbe auswählen (evtl. fortgeschritten):</label>
                                    <input class=\"w-100 form-control form-control-color p-0\" type=\"color\" id=\"custom\" value=\"\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <hr class=\"w-100 border border-1 border-primary\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <input type=\"submit\" class=\"w-100 btn btn-primary\" value=\"Speichern\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <a class=\"w-100 btn btn-secondary\" href=\"/?view=appearance&action=update&target=color\">Abbrechen</a>
                                </div>
                            </div> 
                        </div>
                    </div>
                </form>   
            </div>
            ";
        }
        // Want to see a specific form for an element to change the image for?
        else if(strcmp($target, "image") === 0 && !empty($element))
        {
            return
            "
                This image upload form cannot be shown yet, due to not implemented.
            ";
        }
        else
        {
            return "Ungültige Anfrage - Formular nicht vorhanden.";
        }
    }

    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0, array $validElements = [], array $validColors = []): void
    {
        if(empty($companyData))
        {
            $this->showInvalidRequest([]);
        }
        else
        {
            if(strcmp($action, "all") === 0)
            {
                $feedContentHTML =
                "
                <div class=\"container-fluid\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-12\">
                            <span class=\"fs-2 fw-bold\">Darstellung verwalten</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=color\">Farben ändern 🎨️</a>
                        </div>
                    </div>
                    <div class=\"row mb-5\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=image\">Grafiken ändern 📸</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=account\">Zurück</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else if(strcmp($action, "update") === 0)
            {
                if(strcmp($target, "color") === 0)
                {
                    // Is an overview requested to show all changeable elements which do exist?
                    if(empty($element))
                    {
                        $validColorElements = $validElements["color"];
                        $validColorElementsKeys = array_keys($validColorElements);

                        $buttonsHTML = "";

                        foreach($validColorElementsKeys as $validColorElementsKey)
                        {
                            $validColorElementName = $validColorElements[$validColorElementsKey];

                            $buttonsHTML .= $this->genButton("color", $validColorElementsKey, $validColorElementName);
                        }

                        $feedContentHTML =
                        "
                        <div class=\"container-fluid\">
                            <div class=\"row mt-3 mb-5\">
                                <div class=\"col-12\">
                                    <span class=\"fs-2 fw-bold\">Farbe ändern von</span>
                                </div>
                            </div>
                            $buttonsHTML
                            <div class=\"row mt-5 mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"btn btn-secondary w-100\" href=\"/?view=appearance&action=all\">Zurück</a>
                                </div>
                            </div>
                        </div>
                        ";

                        if(strcmp($target, "color") === 0)
                        {

                        }
                        else if(strcmp($target, "image") === 0)
                        {
                            $feedContentHTML = "Requested overview for all image update forms not implemented yet.";
                        }
                        else
                        {
                            $this->showInvalidRequest($companyData);
                        }
                    }
                    else // = want to change the color of a specific element.
                    {
                        $feedContentHTML = $this->genColorForm($target, $element, $validColors);
                    }

                    /*
                    if($messageValue === 0)
                    {
                        // Show normal update form
                        $this->showForm($companyData, "color", $element);
                    }
                    else if($messageValue === 1)
                    {
                        // Updated successfully
                    }
                    else if($messageValue === -1)
                    {
                        // Update failed
                    }
                    else
                    {
                        $this->showInvalidRequest($companyData);
                    }
                    */
                }
                else if(strcmp($target, "image") === 0)
                {
                    $this->showInvalidRequest($companyData);
                }
                else
                {
                    $this->showInvalidRequest($companyData);
                }
            }
            else
            {
                $this->showInvalidRequest($companyData);
            }

            $feedHTML = $this->globalView->genFeed(null, $feedContentHTML);

            $body =
            "
            <div class=\"container-fluid\">
                $feedHTML
            </div>
            ";

            $this->globalView->show($companyData, $body);
        }
    }
}