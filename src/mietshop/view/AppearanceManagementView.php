<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

class AppearanceManagementView implements IAppearanceManagementView
{
    private IHtmlFactory $htmlFactory;
    private IComponentFactory $componentFactory;

    private IGlobalView $globalView;
    private IErrorView $errorView;

    public function __construct(IHtmlFactory $htmlFactory, IComponentFactory $componentFactory, IGlobalView $globalView, IErrorView $errorView)
    {
        $this->htmlFactory = $htmlFactory;
        $this->componentFactory = $componentFactory;

        $this->globalView = $globalView;
        $this->errorView = $errorView;
    }

    private function showPageFeed(array $companyData, array $appearanceData, string $feedContentHTML): void
    {
        $feedHTML = $this->componentFactory->genFeed($appearanceData, null, $feedContentHTML);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }

    private function genFormColorRadioButton(string $bootstrapColor, bool $isChecked) : string
    {
        $possiblyCheckedProperty = $isChecked ? " checked" : "";

        return
        "
        <div class=\"row darker-interact p-0 mb-3 bg-$bootstrapColor rounded border border-1 border-black align-items-center\">
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

    public function showColorUpdateFailed(array $companyData, array $appearanceData, string $element, string $custom) : void
    {
        $feedContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/paint-bucket.svg", "/?view=appearance&action=update&target=Color", "/?view=appearance&action=update&target=Color&element=$element&custom=$custom", "Farbe ändern fehlgeschlagen", "Es scheint so, als wenn die Farbe nicht geändert werden konnte. Entweder haben Sie eine falsche Farbe angegeben (z.B. im falschen Format) oder es liegt derzeit eine technische Störung vor. Bitte versuchen Sie es (später) nochmal.");

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }

    public function showColorUpdateSuccessful(array $companyData, array $appearanceData, string $element, string $custom) : void
    {
        $feedContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/paint-bucket.svg", "/?view=appearance&action=update&target=Color", "/?view=appearance&action=update&target=Color&element=$element&custom=$custom", "Farbe erfolgreich geändert!");

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }

    private function genCustomColorForm(array $appearanceData, string $element, string $hexCodeColor) : string
    {
        $hexCodeHint = "";

        if(!empty($hexCodeColor))
        {
            $hexCodeHint = " ( derzeit $hexCodeColor )";
        }

        $spanEdit = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Bearbeiten");

        $labelChooseColor = $this->htmlFactory->genLabelText($appearanceData, "Farbe auswählen$hexCodeHint:", "colorPicker");
        $labelEnterHexCode = $this->htmlFactory->genLabelText($appearanceData, "Farbcode / RGB Hex Code eingeben:", "colorHex");

        $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "", "value=\"Speichern\" type=\"submit\"");
        $aCancelButton = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Abbrechen", "/?view=appearance&action=update&target=Color&element=$element&custom=no", [ "w-100" ]);

        $hr = $this->htmlFactory->genHr($appearanceData);

        return
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row mb-1\">
                <div class=\"col-12 text-center\">
                    $spanEdit
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            <form class=\"row form\" action=\"/\" method=\"post\">
                <input type=\"hidden\" name=\"view\" value=\"appearance\">
                <input type=\"hidden\" name=\"action\" value=\"update\">
                <input type=\"hidden\" name=\"target\" value=\"Color\">
                <input type=\"hidden\" name=\"element\" value=\"$element\">
                <input type=\"hidden\" name=\"custom\" value=\"yes\">
                <div class=\"col-12\">
                    <div class=\"container-fluid\">
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0 w-100\">
                                $labelChooseColor
                                <input class=\"w-100 form-control form-control-color p-0\" name=\"colorPicked\" type=\"color\" id=\"colorPicker\" value=\"$hexCodeColor\">
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0\">
                                $hr
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0 w-100\">
                                $labelEnterHexCode
                                <input class=\"w-100 form-control\" placeholder=\"z.B. #11223344 oder #112233\" name=\"colorHexCode\" type=\"text\" id=\"colorHex\" value=\"\">
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0\">
                                $hr
                            </div>
                        </div>
                        <div class=\"row mb-4\">
                            <div class=\"col-12 p-0\">
                                $inputSubmitButton
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12 p-0\">
                                $aCancelButton
                            </div>
                        </div> 
                    </div>
                </div>
            </form>   
        </div>
        ";
    }

    public function showCustomColorForm(array $companyData, array $appearanceData, string $element, string $hexCodeColor) : void
    {
        $feedContentHTML = $this->genCustomColorForm($appearanceData, $element, $hexCodeColor);

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }

    private function genDefaultColorForm(array $appearanceData, string $element, array $validColors, string $selectedBootstrapColor) : string
    {
        $colorRadioButtonsHTML = $this->genFormColorRadioButtons($validColors, $selectedBootstrapColor);

        $hr = $this->htmlFactory->genHr($appearanceData);

        $spanEditTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Bearbeiten");

        $aButtonCustomColor = $this->htmlFactory->genAButtonInteresting($appearanceData, "Benutzerdefinierte Farbe festlegen", "/?view=appearance&action=update&target=Color&element=$element&custom=yes", [ "w-100" ]);

        $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "w-100", "btn" ], "", "value=\"Speichern\" type=\"submit\"");
        $aButtonCancel = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Abbrechen", "/?view=appearance&action=update&target=Color", [ "w-100" ]);

        return
        "
        <div class=\"container-fluid\">
            <div class=\"row mb-1\">
                <div class=\"col-12 p-0 text-center\">
                    $spanEditTitle
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12 p-0\">
                    $hr
                </div>
            </div>
            <form class=\"row form\" action=\"/\" method=\"post\">
                <input type=\"hidden\" name=\"view\" value=\"appearance\">
                <input type=\"hidden\" name=\"action\" value=\"update\">
                <input type=\"hidden\" name=\"target\" value=\"Color\">
                <input type=\"hidden\" name=\"element\" value=\"$element\">
                <input type=\"hidden\" name=\"custom\" value=\"no\">
                <div class=\"col-12\">
                    <div class=\"container-fluid p-0\">
                        $colorRadioButtonsHTML
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0\">
                                $hr
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0\">
                                $aButtonCustomColor
                            </div>
                        </div>
                        <div class=\"row mb-2\">
                            <div class=\"col-12 p-0\">
                                $hr
                            </div>
                        </div>
                        <div class=\"row mb-4\">
                            <div class=\"col-12 p-0\">
                                $inputSubmitButton
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-12 p-0\">
                                $aButtonCancel
                            </div>
                        </div> 
                    </div>
                </div>
            </form>   
        </div>
        ";
    }

    public function showDefaultColorForm(array $companyData, array $appearanceData, string $element, array $validColors, string $selectedBootstrapColor) : void
    {
        $feedContentHTML = $this->genDefaultColorForm($appearanceData, $element, $validColors, $selectedBootstrapColor);

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }

    private function genAllColorElements(array $appearanceData, array $validColorElements) : string
    {
        $validColorElementsKeys = array_keys($validColorElements);

        $buttonsHTML = "";

        for($i = 0; $i < sizeof($validColorElementsKeys); $i++)
        {
            $validColorElementsKey = $validColorElementsKeys[$i];

            $validColorElementName = $validColorElements[$validColorElementsKey];

            $buttonHTML = $this->htmlFactory->genAButtonInteresting($appearanceData, $validColorElementName, "/?view=appearance&action=update&target=Color&element=$validColorElementsKey&custom=no", [ "w-100" ]);

            if($i < (sizeof($validColorElementsKeys) - 1))
            {
                $mb = "mb-3";
            }
            else
            {
                $mb = "mb-2";
            }

            $buttonsHTML .=
            "
            <div class=\"row $mb\">
                <div class=\"col-12\">
                    $buttonHTML
                </div>
            </div>
            ";
        }

        $spanChangeColorOfTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Farbe ändern von");
        $aButtonReturn = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Zurück", "/?view=appearance&action=all", [ "w-100" ]);

        $hr = $this->htmlFactory->genHr($appearanceData);

        $feedContentHTML =
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row mb-1\">
                <div class=\"col-12 text-center\">
                    $spanChangeColorOfTitle
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            $buttonsHTML
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    $aButtonReturn
                </div>
            </div>
        </div>
        ";

        return $feedContentHTML;
    }

    public function showAllColorElements(array $companyData, array $appearanceData, array $validColorElements) : void
    {
        $feedContentHTML = $this->genAllColorElements($appearanceData, $validColorElements);

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }

    private function genOptions(array $appearanceData) : string
    {
        $spanManageAppearanceTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-2", "fw-bold", "lh-1" ], "Darstellung verwalten");
        $aButtonChangeColor = $this->htmlFactory->genAButtonInteresting($appearanceData, "Farben ändern 🎨️", "/?view=appearance&action=update&target=Color", [ "w-100" ]);
        $aButtonChangeImage = $this->htmlFactory->genAButtonInteresting($appearanceData, "Grafiken ändern 📸", "/?view=appearance&action=update&target=Image", [ "w-100" ]);
        $aButtonReturn = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Zurück", "/?view=account", [ "w-100" ]);

        $hr = $this->htmlFactory->genHr($appearanceData);

        $feedContentHTML =
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row mb-1\">
                <div class=\"col-12 text-center\">
                    $spanManageAppearanceTitle
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            <div class=\"row mb-3\">
                <div class=\"col-12\">
                    $aButtonChangeColor
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $aButtonChangeImage
                </div>
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-12\">
                    $aButtonReturn
                </div>
            </div>
        </div>
        ";

        return $feedContentHTML;
    }

    public function showOptions(array $companyData, array $appearanceData) : void
    {
        $feedContentHTML = $this->genOptions($appearanceData);

        $this->showPageFeed($companyData, $appearanceData, $feedContentHTML);
    }
}