<?php

namespace src\mietshop\view\html;

class ComponentFactory implements IComponentFactory
{
    private IHtmlFactory $htmlFactory;

    public function __construct(IHtmlFactory $htmlFactory)
    {
        $this->htmlFactory = $htmlFactory;
    }

    /**
     * Generates a default row,
     * usually suitable for most responsive webpages.
     *
     * @param string $contentHTML Simply the HTML code for your Bootstrap row.
     * @param array $colorElementsCaseSensitive
     * @param array $appearanceData
     * @param array $styles
     * @param array $classes Additional classes you can attach.
     * @return string Row HTML as described.
     *
     * preGenDefaultRow
     */
    public function genResponsiveRow(string $contentHTML, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes) : string
    {
        $columnLeft = $this->htmlFactory->genNonSelfClosingElement("div", [], [], [], [ "col-1", "col-xl-3", "col-xxl-4" ], "");
        $columnCenter = $this->htmlFactory->genNonSelfClosingElement("div", [], [], [], [ "col-10", "col-xl-6", "col-xxl-4" ], $contentHTML);
        $columnRight = $this->htmlFactory->genNonSelfClosingElement("div", [], [], [], [ "col-1", "col-xl-3", "col-xxl-4" ], "");

        $rowContent = $columnLeft.$columnCenter.$columnRight;

        $classes[] = "row";

        $row = $this->htmlFactory->genNonSelfClosingElement("div", $colorElementsCaseSensitive, $appearanceData, $styles, $classes, $rowContent);

        return $row;
    }

    public function genFeed(array $appearanceData, ?string $pictureURL, string $descriptionHTML) : string
    {
        if(is_string($pictureURL))
        {
            $rowContent =
            "
            <div class=\"col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-4 align-self-center\">
                <div class=\"border border-primary rounded bg-light\" style=\"display: flex; justify-content: center; align-items: center; text-align: center; min-width: 120px; width: 120px; min-height: 120px; max-width: 120px; height: 120px;\">
                    <img class=\"img-fluid\" style=\"max-width: 118px; max-height: 118px;\" src=\"$pictureURL\" alt=\"$pictureURL\">
                </div>
            </div>
            <div class=\"col-6 col-sm-8 col-md-9 col-lg-9 col-xl-9 col-xxl-8 align-self-center\">
                <div>$descriptionHTML</div>
            </div>
            ";
        }
        else
        {
            $rowContent =
            "
            <div class=\"col-12 p-0\">
                $descriptionHTML
            </div>
            ";
        }

        $rowHTML = $this->htmlFactory->genNonSelfClosingElement("div", [], [], [], [ "row", "p-3" ], $rowContent);

        $containerHTML = $this->htmlFactory->genNonSelfClosingElement("div", [ "Window", "Frame" ], $appearanceData, [], [ "container-fluid", "ms-0", "mt-0", "me-0", "mb-5", "rounded-3" ], $rowHTML);

        return $this->genResponsiveRow($containerHTML, [], [], [], []);
    }

    public function genActionResultMessage(array $appearanceData, string $svgPath, string $returnHref, string $actionRepeatHref, string $title, string $description = "") : string
    {
        $svgForText = $this->htmlFactory->genTextSVG($svgPath);
        $spanTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fw-bold", "inline-block", "lh-1" ], "$title&nbsp;$svgForText");
        $spanDescription = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "w-100" ], $description);

        $aReturnButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "Zur Übersicht", $returnHref);

        if(!empty($actionRepeatHref))
        {
            $aRepeatActionButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Aktion wiederholen", $actionRepeatHref);
            $divRepeatActionButton =
            "
            <div class=\"row mb-3\">
                <div class=\"col-12\">
                    $aRepeatActionButton
                </div>
            </div>
            ";
        }
        else
        {
            $divRepeatActionButton = "";
        }


        $hr = $this->htmlFactory->genHr($appearanceData);

        $descriptionHTML = (!empty($description)) ?
        "
        <div class=\"row mb-2\">
            <div class=\"col-12\">
                $spanDescription
            </div>
        </div>
        <div class=\"row mb-2\">
            <div class=\"col-12\">
                $hr
            </div>
        </div>
        " : "";

        return
        "
        <div class=\"container-fluid p-0\">
            <div class=\"row mb-1 fs-2 text-center\">
                $spanTitle
            </div>
            <div class=\"row mb-2\">
                <div class=\"col-12\">
                    $hr
                </div>
            </div>
            $descriptionHTML
            $divRepeatActionButton
            <div class=\"row mt-2\">
                <div class=\"col-12\">
                    $aReturnButton
                </div>
            </div>
        </div>
        ";
    }

    private function preGenFileUploadFormElements(array $appearanceData, string $fileUploadButtonLabel, string $formFileUploadButtonName, string $formSubmitButtonTitle, string $returnHref, array $hiddenInput = [], array $acceptMimeTypes = []) : array
    {
        $hr = $this->htmlFactory->genHr($appearanceData);

        $aButtonCancel = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Abbrechen", $returnHref, [ "w-100" ]);

        $labelUploadImage = $this->htmlFactory->genLabelText($appearanceData, $fileUploadButtonLabel, $formFileUploadButtonName);

        $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "", "value=\"$formSubmitButtonTitle\" type=\"submit\"");

        $hiddenInputHTML = "";

        foreach($hiddenInput as $hiddenInputElement)
        {
            if(isset($hiddenInputElement["name"]) && isset($hiddenInputElement["value"]))
            {
                $hiddenInputName = $hiddenInputElement["name"];
                $hiddenInputValue = $hiddenInputElement["value"];

                if(is_string($hiddenInputName) && is_string($hiddenInputValue))
                {
                    $hiddenInputHTML .= $this->htmlFactory->genSelfClosingElement("input", [], $appearanceData, [], [], "", "type=\"hidden\" name=\"$hiddenInputName\" value=\"$hiddenInputValue\"");
                }
            }
        }

        $acceptMimeTypesProperty = empty($acceptMimeTypes) ? "" : " accept=\"";

        for($i = 0; $i < sizeof($acceptMimeTypes); $i++)
        {
            $acceptMimeType = $i < (sizeof($acceptMimeTypes) - 1) ? "{$acceptMimeTypes[$i]}, " : $acceptMimeTypes[$i];

            $acceptMimeTypesProperty .= $acceptMimeType;
        }

        $acceptMimeTypesProperty .= empty($acceptMimeTypes) ? "" : "\"";

        return [ "hr" => $hr, "aButtonCancel" => $aButtonCancel, "labelUploadImage" => $labelUploadImage, "inputSubmitButton" => $inputSubmitButton, "hiddenInputHTML" => $hiddenInputHTML, "acceptMimeTypesProperty" => $acceptMimeTypesProperty];
    }

    public function genMultipleFileUploadForm(array $appearanceData, string $formAction, string $fileUploadButtonLabel, string $formFileUploadButtonName, string $formSubmitButtonTitle, string $returnHref, array $hiddenInput = [], array $acceptMimeTypes = []) : string
    {
        $preGeneratedForm = $this->preGenFileUploadFormElements($appearanceData, $fileUploadButtonLabel, $formFileUploadButtonName, $formSubmitButtonTitle, $returnHref, $hiddenInput, $acceptMimeTypes);

        $inputUploadButton = $this->htmlFactory->genSelfClosingElement("input", [], $appearanceData, [], [ "form-control" ], "", "type=\"file\" id=\"$formFileUploadButtonName\" name=\"{$formFileUploadButtonName}[]\" multiple{$preGeneratedForm["acceptMimeTypesProperty"]}");

        return
        "
        <form class=\"row form\" action=\"$formAction\" method=\"post\" enctype=\"multipart/form-data\">
            {$preGeneratedForm["hiddenInputHTML"]}
            <div class=\"col-12\">
                <div class=\"container-fluid p-0\">
                    <div class=\"row mb-2\">
                        <div class=\"col-12 p-0\">
                            {$preGeneratedForm["labelUploadImage"]}
                            $inputUploadButton
                        </div>
                    </div>
                    <div class=\"row mb-2\">
                        <div class=\"col-12 p-0\">
                            {$preGeneratedForm["hr"]}
                        </div>
                    </div>
                    <div class=\"row mb-4\">
                        <div class=\"col-12 p-0\">
                            {$preGeneratedForm["inputSubmitButton"]}
                        </div>
                    </div> 
                    <div class=\"row\">
                        <div class=\"col-12 p-0\">
                            {$preGeneratedForm["aButtonCancel"]}
                        </div>
                    </div> 
                </div>
            </div>
        </form>   
        ";
    }
}