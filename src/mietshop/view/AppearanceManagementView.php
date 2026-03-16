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

    private function showPageFeed(array $companyData, string $feedContentHTML): void
    {
        $feedHTML = $this->globalView->genFeed(null, $feedContentHTML);

        $body =
        "
        <div class=\"container-fluid\">
            $feedHTML
        </div>
        ";

        $this->globalView->show($companyData, $body);
    }

    private function genButton(string $urlTargetValue, string $urlElementValue, string $title) : string
    {
        return
        "
        <div class=\"row mb-3\">
            <div class=\"col-12\">
                <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=$urlTargetValue&element=$urlElementValue&custom=no\">$title</a>
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButton(string $bootstrapColor, bool $isChecked) : string
    {
        $possiblyCheckedProperty = $isChecked ? " checked" : "";

        return
        "
        <div class=\"row darker-hover p-0 mb-3 bg-$bootstrapColor rounded border border-1 border-black align-items-center\">
            <div class=\"col-1 ps-0 h-100 d-inline\">
                <input type=\"radio\" id=\"$bootstrapColor\" name=\"colorBootstrap\" class=\"form-check-input ms-3 border border-1 border-black d-inline-block\" value=\"$bootstrapColor\"$possiblyCheckedProperty>
            </div>
            <div class=\"col-11 pe-0 h-100 d-inline\">
                <label class=\"form-check-label fw-bold w-100 p-2 d-inline-block\" for=\"$bootstrapColor\"><span class=\"fs-6 p-1 bg-dark-subtle rounded border border-1 border-black text-black d-inline\">$bootstrapColor</span></label>
            </div>
        </div>
        ";
    }

    private function genFormColorRadioButtons(array $validColors, string $selectedBootstrapColor) : string
    {
        $colorRadioButtons = "";

        foreach($validColors as $acceptedBootstrapColor)
        {
            // If a Bootstrap color name was chosen or is saved, then select the correct one.
            if(strcmp($selectedBootstrapColor, $acceptedBootstrapColor) === 0)
                $isChecked = true;
            else
                $isChecked = false;

            // Some radio button needs to be checked by default, because otherwise no radio button is transmitted via POST request (which is interpreted as wrong request - because programmed this way)
            $colorRadioButtons .= $this->genFormColorRadioButton($acceptedBootstrapColor, $isChecked);
        }

        return $colorRadioButtons;
    }

    private function genColorUpdateFailed(string $element, string $custom) : string
    {
        return
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
                    <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Color&element=$element&custom=$custom\">Nochmal versuchen</a>
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

    public function showColorUpdateFailed(array $companyData, string $element, string $custom) : void
    {
        $feedContentHTML = $this->genColorUpdateFailed($element, $custom);

        $this->showPageFeed($companyData, $feedContentHTML);
    }

    private function genColorUpdateSuccessful(string $element, string $custom) : string
    {
        return
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
                    <a class=\"btn btn-primary w-100\" href=\"/?view=appearance&action=update&target=Color&element=$element&custom=$custom\">Farbe erneut ändern</a>
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

    public function showColorUpdateSuccessful(array $companyData, string $element, string $custom) : void
    {
        $feedContentHTML = $this->genColorUpdateSuccessful($element, $custom);

        $this->showPageFeed($companyData, $feedContentHTML);
    }

    private function genCustomColorForm(string $element, string $hexCodeColor) : string
    {
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
                <input type=\"hidden\" name=\"custom\" value=\"yes\">
                <div class=\"col-12\">
                    <div class=\"container-fluid p-0\">
                        <div class=\"row mb-3\">
                            <div class=\"col-12 p-0 w-100\">
                                <label class=\"form-label fw-bold w-100 fs-6\" for=\"colorPicker\">Farbe auswählen (außer schwarz):</label>
                                <input class=\"w-100 form-control form-control-color p-0\" name=\"colorPicked\" type=\"color\" id=\"colorPicker\" value=\"$hexCodeColor\">
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12 p-0\">
                                <hr class=\"w-100 border border-1 border-primary\">
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12 p-0 w-100\">
                                <label class=\"form-label fw-bold w-100 fs-6\" for=\"colorHex\">Farbcode eingeben [ RGB(A) Hex Code ]:</label>
                                <input class=\"w-100 form-control\" placeholder=\"z.B. #11223344 oder #112233\" name=\"colorHexCode\" type=\"text\" id=\"colorHex\" value=\"\">
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
                                <a class=\"w-100 btn btn-secondary\" href=\"/?view=appearance&action=update&target=Color&element=$element&custom=no\">Abbrechen</a>
                            </div>
                        </div> 
                    </div>
                </div>
            </form>   
        </div>
        ";
    }

    public function showCustomColorForm(array $companyData, string $element, string $hexCodeColor) : void
    {
        $feedContentHTML = $this->genCustomColorForm($element, $hexCodeColor);

        $this->showPageFeed($companyData, $feedContentHTML);
    }

    private function genDefaultColorForm(string $element, array $validColors, string $selectedBootstrapColor) : string
    {
        $colorRadioButtonsHTML = $this->genFormColorRadioButtons($validColors, $selectedBootstrapColor);

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
                        $colorRadioButtonsHTML
                        <div class=\"row mb-3\">
                            <div class=\"col-12 p-0\">
                                <hr class=\"w-100 border border-1 border-primary\">
                            </div>
                        </div>
                        <div class=\"row mb-3\">
                            <div class=\"col-12 p-0\">
                                <a class=\"w-100 btn btn-primary\" href=\"/?view=appearance&action=update&target=Color&element=$element&custom=yes\">Benutzerdefinierte Farbe festlegen</a>
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

    public function showDefaultColorForm(array $companyData, string $element, array $validColors, string $selectedBootstrapColor) : void
    {
        $feedContentHTML = $this->genDefaultColorForm($element, $validColors, $selectedBootstrapColor);

        $this->showPageFeed($companyData, $feedContentHTML);
    }

    private function genAllColorElements(array $validColorElements) : string
    {
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

        return $feedContentHTML;
    }

    public function showAllColorElements(array $companyData, array $validColorElements) : void
    {
        $feedContentHTML = $this->genAllColorElements($validColorElements);

        $this->showPageFeed($companyData, $feedContentHTML);
    }

    private function genOptions() : string
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

        return $feedContentHTML;
    }

    public function showOptions(array $companyData) : void
    {
        $feedContentHTML = $this->genOptions();

        $this->showPageFeed($companyData, $feedContentHTML);
    }
}