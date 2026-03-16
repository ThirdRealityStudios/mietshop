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

    private function genFormColorRadioButton(string $acceptedBsColorName, string $labelText, string $id, string $postValue, bool $isChecked) : string
    {
        $possiblyCheckedProperty = $isChecked ? " checked" : "";

        return
        "
        <div class=\"row darker-hover p-0 mb-3 bg-$acceptedBsColorName rounded border border-1 border-black align-items-center\">
            <div class=\"col-1 ps-0 h-100 d-inline\">
                <input type=\"radio\" id=\"$id\" name=\"colorBootstrap\" class=\"form-check-input ms-3 border border-1 border-black d-inline-block\" value=\"$postValue\"$possiblyCheckedProperty>
            </div>
            <div class=\"col-11 pe-0 h-100 d-inline\">
                <label class=\"form-check-label fw-bold w-100 p-2 d-inline-block\" for=\"$id\"><span class=\"fs-6 p-1 bg-dark-subtle rounded border border-1 border-black text-black d-inline\">$labelText</span></label>
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButtons(array $validColors, string $oldColorSaved, bool $isCustomColor) : string
    {
        $colorRadioButtons = "";

        $count = 0;

        foreach($validColors as $acceptedBsColorName)
        {
            if($isCustomColor) // If a custom hex digit color is saved, then there is no Bootstrap color name chosen, so choose the first always.
            {
                if($count === 0)
                {
                    $isChecked = true;
                }
                else
                {
                    $isChecked = false;
                }
            }
            else // If a Bootstrap color name was chosen or is saved, then select the correct one.
            {
                if(strcmp($oldColorSaved, $acceptedBsColorName) === 0)
                {
                    $isChecked = true;
                }
                else
                {
                    $isChecked = false;
                }
            }

            // Some radio button needs to be checked by default, because otherwise no radio button is transmitted via POST request (which is interpreted as wrong request - because programmed this way)
            $colorRadioButtons .= $this->genFormColorRadioButton($acceptedBsColorName, $acceptedBsColorName, $acceptedBsColorName, $acceptedBsColorName, $isChecked);

            $count++;
        }

        return $colorRadioButtons;
    }


    // // Determine if the old color is a Bootstrap color or a custom hex code color.
    private function genColorForm(string $element, array $validColors, string $oldColorSaved, bool $isCustomColor, int $messageValue) : string
    {
        $colorRadioButtonsHTML = $this->genFormColorRadioButtons($validColors, $oldColorSaved, $isCustomColor);

        // See if a custom hex code color was set before or is saved. If so, then use it. Otherwise, leave it clear, so the Bootstrap color will only appear.
        $possibleHexColorCode = $isCustomColor ? $oldColorSaved : "";

        // Want to see a specific form for an element to change the color for?
        if(!empty($element))
        {
            $possibleColorBootstrapBSClass = ($messageValue === -2) ? "is-invalid text-danger" : "";

            $possibleColorPickedBSClass = ($messageValue === -3) ? " is-invalid" : "";
            $possibleLabelPickedBSClass = ($messageValue === -3) ? " fw-bold text-danger" : "";

            $possibleHexColorCodeBSClass = ($messageValue === -4) ? " is-invalid" : "";
            $possibleLabelHexCodeBSClass = ($messageValue === -4) ? " fw-bold text-danger" : "";

            return
            "
            <div class=\"container-fluid\">
                <div class=\"row mb-4\">
                    <div class=\"col-12 p-0\">
                        <p class=\"fs-2 fw-bold\">Bearbeiten</p>
                    </div>
                </div>
                <form class=\"row form\" action=\"/\" method=\"post\">
                    <input type=\"hidden\" name=\"view\" value=\"appearance\">
                    <input type=\"hidden\" name=\"action\" value=\"update\">
                    <input type=\"hidden\" name=\"target\" value=\"Color\">
                    <input type=\"hidden\" name=\"element\" value=\"$element\">
                    <div class=\"col-12\">
                        <div class=\"container-fluid p-0\">
                            <div class=\"$possibleColorBootstrapBSClass\">
                                $colorRadioButtonsHTML
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <hr class=\"w-100 border border-1 border-primary\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0 w-100\">
                                    <label class=\"form-label fw-bold w-100 fs-6$possibleLabelPickedBSClass\" for=\"colorPicker\">Eigene Farbe auswählen (außer schwarz):</label>
                                    <input class=\"w-100 form-control form-control-color p-0$possibleColorPickedBSClass\" name=\"colorPicked\" type=\"color\" id=\"colorPicker\" value=\"$possibleHexColorCode\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0\">
                                    <hr class=\"w-100 border border-1 border-primary\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12 p-0 w-100\">
                                    <label class=\"form-label fw-bold w-100 fs-6$possibleLabelHexCodeBSClass\" for=\"colorHex\">Farb-Code eingeben (RGBA Hex Code):</label>
                                    <input class=\"w-100 form-control$possibleHexColorCodeBSClass\" placeholder=\"z.B. #11223344\" name=\"colorHexCode\" type=\"text\" id=\"colorHex\" value=\"$possibleHexColorCode\">
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
                                    <a class=\"w-100 btn btn-secondary\" href=\"/?view=appearance&action=update&target=Color\">Abbrechen</a>
                                </div>
                            </div> 
                        </div>
                    </div>
                </form>   
            </div>
            ";
        }
        else
        {
            return "Ungültige Anfrage - Formular nicht vorhanden.";
        }
    }

    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0, array $validElements = [], array $validColors = [], string $oldColorSaved = "", bool $isCustomColor = false): void
    {
        if(empty($companyData))
        {
            $this->showInvalidRequest([]);
        }
        else
        {
            $feedContentHTML = "Unknown page";

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
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Color\">Farben ändern 🎨️</a>
                        </div>
                    </div>
                    <div class=\"row mb-5\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Image\">Grafiken ändern 📸</a>
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
                if(strcmp($target, "Color") === 0)
                {
                    // Is an overview requested to show all changeable elements which do exist?
                    if(empty($element))
                    {
                        $validColorElements = $validElements["Color"];
                        $validColorElementsKeys = array_keys($validColorElements);

                        $buttonsHTML = "";

                        foreach($validColorElementsKeys as $validColorElementsKey)
                        {
                            $validColorElementName = $validColorElements[$validColorElementsKey];

                            $buttonsHTML .= $this->genButton("Color", $validColorElementsKey, $validColorElementName);
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
                    else if($messageValue <= -2 || $messageValue === 0) // = want to change the color of a specific element. Or was specific field marked that is invalid?
                    {
                        $feedContentHTML = $this->genColorForm($element, $validColors, $oldColorSaved, $isCustomColor, $messageValue);
                    }
                    else if($messageValue === -1)
                    {
                        $feedContentHTML =
                        "
                        <div class=\"container-fluid p-0\">
                            <div class=\"row mt-3 mb-5\">
                                <div class=\"col-2\" style=\"background-image: url('/img/symbol/paint-bucket-danger.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                                <div class=\"col-10\">
                                    <span class=\"fs-2 fw-bold text-danger\">Farbe ändern fehlgeschlagen</span>
                                </div>
                            </div>
                            <div class=\"row mb-5\">
                                <div class=\"col-12\">
                                    <span class=\"fs-6 w-100\">Es scheint so, als wenn die Farbe nicht geändert werden konnte. Entweder haben Sie eine falsche Farbe angegeben (z.B. im falschen Format) oder es liegt derzeit eine technische Störung vor. Bitte versuchen Sie es (später) nochmal.</span>
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Color&element=$element\">Nochmal versuchen</a>
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"btn btn-secondary w-100\" href=\"/?view=appearance&action=update&target=Color\">Zurück zur Übersicht</a>
                                </div>
                            </div>
                        </div>
                        ";
                    }
                    else if($messageValue === 1)
                    {
                        $feedContentHTML =
                        "
                        <div class=\"container-fluid p-0\">
                            <div class=\"row mt-3 mb-5\">
                                <div class=\"col-2\" style=\"background-image: url('/img/symbol/paint-bucket-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                                <div class=\"col-10\">
                                    <span class=\"fs-2 fw-bold text-success\">Farbe ändern erfolgreich</span>
                                </div>
                            </div>
                            <div class=\"row mb-5\">
                                <div class=\"col-12\">
                                    <span class=\"fs-6 w-100\">Farbe erfolgreich geändert ✓</span>
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Color&element=$element\">Farbe erneut ändern</a>
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <a class=\"btn btn-secondary w-100\" href=\"/?view=appearance&action=update&target=Color\">Zurück zur Übersicht</a>
                                </div>
                            </div>
                        </div>
                        ";
                    }
                    else
                    {
                        $this->showInvalidRequest($companyData);
                    }
                }
                else if(strcmp($target, "Image") === 0)
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