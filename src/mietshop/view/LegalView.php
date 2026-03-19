<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\ILegalView;
use src\mietshop\contracts\ITextFormatView;

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
    private IGlobalView $globalView;
    private IErrorView $errorView;
    private ITextFormatView $textFormatView;

    public function __construct(IGlobalView $globalView, IErrorView  $errorView, ITextFormatView $textFormatView)
    {
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

        $optionalEditButton = $this->globalView->genNonClosingElement("a", [ "Uninteresting" ], $appearanceData, [], [ "btn", "w-100" ], "Bearbeiten", "/?view=$legalTextView&action=update");
        $optionalEditButtonRow = $showEditButton ? "<div class=\"row mb-4\"><div class=\"col-12\">$optionalEditButton</div></div>" : "";

        if(empty($actionValue))
        {
            $legalTextTitle = $this->globalView->genNonClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-3", "fw-bold", "lh-1" ], $legalTextTitleLong);
            $legalTextDescription = $this->globalView->genNonClosingElement("span", [ "Text" ], $appearanceData, [ "white-space: pre-line;" ], [ "fs-6" ], $legalTextHTMLSafeFormatted);
            $aLandingPageButton = $this->globalView->genAButtonInteresting($appearanceData, "Zur Startseite", "/");

            $legalTextContentHTML =
            "
            <div class=\"container-fluid p-0\">
                <div class=\"row mb-4\">
                    <div class=\"col-12 text-center\">
                        $legalTextTitle
                    </div>
                </div>
                $optionalEditButtonRow
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        $legalTextDescription
                    </div>
                </div>
                <div class=\"row\">
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
                $legalTextLabelBSClass = "";
                $legalTextInputBSClass = "";
                if($messageValue === -1)
                {
                    $legalTextLabelBSClass = " text-danger";
                    $legalTextInputBSClass = " is-invalid";
                }

                $legalTextContentHTML =
                 "
                 <div class=\"container-fluid\">
                     <div class=\"row\">
                         <div class=\"col-12\">
                             <p class=\"fs-2 fw-bold\">Bearbeiten</p>
                         </div>
                     </div>
                     <form class=\"row form\" action=\"/?view=$legalTextView&action=update\" method=\"post\">
                         <div class=\"col-12\">
                             <div class=\"container-fluid p-0\">
                                 <div class=\"row mb-3\">
                                     <div class=\"col-12\">
                                         <label class=\"form-label $legalTextLabelBSClass\" for=\"$legalTextInputIdentifier\">( max. $legalTextMaxLength Zeichen )</label>
                                         <textarea rows=\"25\" placeholder=\"$legalTextTitleShort hier eingeben ...\" class=\"w-100 h-100 form-control$legalTextInputBSClass\" id=\"$legalTextInputIdentifier\" name=\"$legalTextInputIdentifier\" maxlength=\"$legalTextMaxLength\">{$legalTextHTMLSafe}</textarea>
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
                                        <a class=\"w-100 btn btn-secondary\" href=\"/?view=$legalTextView\">Abbrechen</a>
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
                $legalTextContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-2\" style=\"background-image: url('/img/symbol/shield-fill-check-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                        <div class=\"col-10\">
                           <span class=\"fs-2 fw-bold text-success\">$legalTextTitleShort erfolgreich aktualisiert!</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-success w-100\" href=\"/?view=$legalTextView&action=update\">$legalTextTitleShort weiter bearbeiten</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=$legalTextView\">$legalTextTitleShort aufrufen</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else
            {
                // Invalid request
                $legalTextContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-2\" style=\"background-image: url('/img/symbol/cpu-fill-danger.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                        <div class=\"col-10\">
                           <span class=\"fs-2 fw-bold text-danger\">Ungültige Anfrage</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=$legalTextView\">$legalTextTitleShort aufrufen</a>
                        </div>
                    </div>
                </div>
                ";
            }
        }
        else
        {
            $legalTextContentHTML = "Ungültige Anfrage";
        }

        $feedHTML = $this->globalView->genFeed($appearanceData, null, $legalTextContentHTML);

        $this->globalView->show($companyData, $appearanceData, $feedHTML);
    }
}