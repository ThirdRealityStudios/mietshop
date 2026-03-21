<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\ILegalView;
use src\mietshop\contracts\ITextFormatView;
use src\mietshop\view\html\IComponentFactory;
use src\mietshop\view\html\IHtmlFactory;

/**
 * Meant to visualize the terms of service or privacy policy.
 * NOT (!) meant to be used for the imprint.
 * This is because the visualization and update form of both,
 * the terms of service and privacy policy,
 * are identical.
 * The ones for the imprint are very different designed to work.
 */
class LegalView implements ILegalView
{
    private IHtmlFactory $htmlFactory;
    private IComponentFactory $componentFactory;

    private IGlobalView $globalView;
    private IErrorView $errorView;
    private ITextFormatView $textFormatView;

    public function __construct(IHtmlFactory $htmlFactory, IComponentFactory $componentFactory, IGlobalView $globalView, IErrorView  $errorView, ITextFormatView $textFormatView)
    {
        $this->htmlFactory = $htmlFactory;
        $this->componentFactory = $componentFactory;

        $this->globalView = $globalView;
        $this->errorView = $errorView;
        $this->textFormatView = $textFormatView;
    }

    public function show(array $companyData, array $appearanceData, bool $showEditButton, bool $isPrivacyPolicy, string $actionValue = "", int $messageValue = 0) : void
    {
        $legalTextKey = $isPrivacyPolicy ? "PrivacyPolicy" : "TermsOfService";
        $legalTextTitleLong = $isPrivacyPolicy ? "Datenschutzerklärung" : "Allgemeine Geschäftsbedingungen";
        $legalTextTitleShort = $isPrivacyPolicy ? "Datenschutzerklärung" : "AGBs";
        $legalTextView = $isPrivacyPolicy ? "privacypolicy" : "termsofservice";
        $legalTextInputIdentifier = $isPrivacyPolicy ? "privacyPolicy" : "termsOfService";

        $legalTextUnformatted = $companyData[$legalTextKey][0]; // Unformatted without styles

        $legalTextHTMLSafe = htmlspecialchars($legalTextUnformatted, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $legalTextHTMLSafeFormatted = $this->textFormatView->formatAll($legalTextHTMLSafe);

        $hr = $this->htmlFactory->genHr($appearanceData);

        $optionalEditButton = $this->htmlFactory->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Bearbeiten", "/?view=$legalTextView&action=update");
        $optionalEditButtonRow = $showEditButton ?
        "
        <div class=\"row mt-1 mb-1\">
            <div class=\"col-12\">
                $hr
            </div>
        </div>
        <div class=\"row mb-1\">
            <div class=\"col-12\">
                $optionalEditButton
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-12\">
                $hr
            </div>
        </div>
        " : "";

        if(empty($actionValue))
        {
            $legalTextTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-3", "fw-bold", "lh-1" ], $legalTextTitleLong);
            $legalTextDescription = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [ "white-space: pre-line;" ], [ "fs-6" ], $legalTextHTMLSafeFormatted);
            $aLandingPageButton = $this->htmlFactory->genAButtonInteresting($appearanceData, "Zur Startseite", "/");

            $legalTextContentHTML =
            "
            <div class=\"container-fluid p-0\">
                <div class=\"row\">
                    <div class=\"col-12 text-center\">
                        $legalTextTitle
                    </div>
                </div>
                $optionalEditButtonRow
                <div class=\"row mt-3 mb-3\">
                    <div class=\"col-12\">
                        $legalTextDescription
                    </div>
                </div>
                <div class=\"row mt-4\">
                    <div class=\"col-12\">
                        $aLandingPageButton
                    </div>
                </div>
            </div>
            ";
        }
        else if(strcmp($actionValue, "update") === 0)
        {
            if($messageValue <= 0) // Show the form and highlight the invalid text field, based on $messageValue given.
            {
                $legalTextMaxLength = $companyData[$legalTextKey][1];
                $legalTextInputBSClass = "";
                if($messageValue === -1)
                {
                    $legalTextInputBSClass = " is-invalid";
                }

                $spanLegalTitle = $this->htmlFactory->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-3", "fw-bold" ], "Bearbeiten");
                $labelMaxCharacterHint = $this->htmlFactory->genLabelText($appearanceData, "Maximal $legalTextMaxLength Zeichen erlaubt.", "$legalTextInputIdentifier");

                $inputSubmitButton = $this->htmlFactory->genSelfClosingElement("input", [ "Interesting" ], $appearanceData, [], [ "btn", "w-100" ], "", "value=\"Speichern\" type=\"submit\"");
                $aButtonCancel = $this->htmlFactory->genAButtonUninteresting($appearanceData, "Abbrechen", "/?view=$legalTextView", [ "w-100", "btn" ]);

                $legalTextContentHTML =
                 "
                 <div class=\"container-fluid\">
                     <div class=\"row mb-1\">
                         <div class=\"col-12\">
                             $spanLegalTitle
                         </div>
                     </div>
                     <div class=\"row mb-1\">
                        <div class=\"col-12\">
                            $hr
                        </div>
                    </div>
                     <form class=\"row form\" action=\"/?view=$legalTextView&action=update\" method=\"post\">
                         <div class=\"col-12\">
                             <div class=\"container-fluid p-0\">
                                 <div class=\"row mb-3\">
                                     <div class=\"col-12\">
                                         $labelMaxCharacterHint
                                         <textarea rows=\"25\" placeholder=\"$legalTextTitleShort hier eingeben ...\" class=\"mt-3 w-100 h-100 form-control$legalTextInputBSClass\" id=\"$legalTextInputIdentifier\" name=\"$legalTextInputIdentifier\" maxlength=\"$legalTextMaxLength\">{$legalTextHTMLSafe}</textarea>
                                     </div>
                                 </div>
                                 <div class=\"row mb-3\">
                                    <div class=\"col-12\">
                                        <hr class=\"w-100 border border-1 border-primary\">
                                    </div>
                                </div>
                                <div class=\"row mb-3\">
                                    <div class=\"col-12\">
                                        $hr
                                    </div>
                                </div>
                                <div class=\"row mb-3\">
                                    <div class=\"col-12\">
                                        $inputSubmitButton
                                    </div>
                                </div>
                                <div class=\"row mb-3\">
                                    <div class=\"col-12\">
                                        $aButtonCancel
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </form>   
                </div>
                ";
            }
            else if($messageValue === 1) // Show that update was all ok.
            {
                $legalTextContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/shield-fill-check.svg", "/?view=$legalTextView", "/?view=$legalTextView&action=update", "Datenschutz aktualisiert!");
            }
            else
            {
                // Invalid request
                $legalTextContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/cpu-fill.svg", "/?view=$legalTextView", "", "Ungültige Anfrage");
            }
        }
        else
        {
            $legalTextContentHTML = $this->componentFactory->genActionResultMessage($appearanceData, "/img/symbol/cpu-fill.svg", "/?view=$legalTextView", "", "Ungültige Anfrage");
        }

        $feedHTML = $this->componentFactory->genFeed($appearanceData, null, $legalTextContentHTML);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}