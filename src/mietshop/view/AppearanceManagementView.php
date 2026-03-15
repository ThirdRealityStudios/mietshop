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
        <div class=\"row mb-3\">
            <div class=\"col-12\">
                <label class=\"form-label fw-bold $bsColorClass\" for=\"$id\">$labelText</label>
                <input type=\"radio\" id=\"$id\" name=\"color\" class=\"form-check\" value=\"$postValue\">
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButtons(array $validColors) : string
    {
        $colorRadioButtons = "";

        foreach($validColors as $validColor)
        {
            $bsClass = "bg-".$validColor[0];

            $colorRadioButtons .= $this->genFormColorRadioButton($bsClass, $validColor[1], $validColor[0], $validColor[0]);
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
                    <div class=\"col-12\">
                        <p class=\"fs-2 fw-bold\">Bearbeiten</p>
                    </div>
                </div>
                <form class=\"row form\" action=\"/?view=appearance&action=update&target=$target&element=$element\" method=\"post\">
                    <div class=\"col-12\">
                        <div class=\"container-fluid p-0\">
                            $colorRadioButtonsHTML
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <label class=\"form-label\" for=\"custom\">Eigene Farbe auswählen (evtl. fortgeschritten):</label>
                                    <input type=\"color\" id=\"custom\" class=\"w-100 btn btn-primary\" value=\"\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <hr class=\"w-100 border border-1 border-primary\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <input type=\"submit\" class=\"w-100 btn btn-primary\" value=\"Speichern\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"w-100 btn btn-secondary\" href=\"/?view=appearance&action=update&target=all\">Abbrechen</a>
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
                if((strcmp($target, "color") === 0 || strcmp($target, "image") === 0))
                {
                    // Is an overview requested to show all changeable elements which do exist?
                    if(empty($element))
                    {
                        if(strcmp($target, "color") === 0)
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
                    else // Is a specific element requested to change the color for? (= show submit form for that specific element??)
                    {
                        if(strcmp($target, "color") === 0)
                        {
                            $feedContentHTML = $this->genColorForm($target, $element, $validColors);
                        }
                        else if(strcmp($target, "image") === 0)
                        {
                            $feedContentHTML = "Requested update form for that given element not implemented yet.";
                        }
                        else
                        {
                            $this->showInvalidRequest($companyData);
                        }
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