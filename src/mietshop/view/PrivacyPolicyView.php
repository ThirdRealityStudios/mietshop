<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IPrivacyPolicyView;

class PrivacyPolicyView implements IPrivacyPolicyView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData, bool $showEditButton, string $actionValue = "", int $messageValue = 0) : void
    {
        $privacyPolicyHTMLSafe = htmlspecialchars($companyData["PrivacyPolicy"][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $optionalEditButton = $showEditButton ? "<div class=\"row mb-3\"><div class=\"col-12\"><a class=\"btn btn-secondary\" href=\"/?view=privacypolicy&action=update\">Datenschutzerklärung bearbeiten</a></div></div>" : "";

        if(empty($actionValue))
        {
            $privacyPolicyContentHTML =
            "
            <div class=\"container-fluid p-0\">
                <div class=\"row mb-4 mt-2\">
                    <div class=\"col-12\">
                        <span class=\"fs-2 fw-bold\">Datenschutzerklärung</span>
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        <p style=\"white-space: pre-line;\" class=\"fs-6\">$privacyPolicyHTMLSafe</p>
                    </div>
                </div>
                <div class=\"row mb-3\">
                    <div class=\"col-12\">
                        <a class=\"btn btn-primary\" href=\"/\">Zur Startseite</a>
                    </div>
                </div>
                $optionalEditButton
            </div>
            ";
        }
        else if(strcmp($actionValue, "update") === 0)
        {
            if($messageValue <= 0) // Show the form and highlight the invalid text field, based on $messageValue given.
            {
                $privacyPolicyMaxLength = $companyData["PrivacyPolicy"][1];
                $privacyPolicyLabelBSClass = "";
                $privacyPolicyInputBSClass = "";
                if($messageValue === -1)
                {
                    $privacyPolicyLabelBSClass = " text-danger";
                    $privacyPolicyInputBSClass = " is-invalid";
                }

                $privacyPolicyContentHTML =
                "
                 <div class=\"container-fluid\">
                     <div class=\"row\">
                         <div class=\"col-12\">
                             <p class=\"fs-1\">Datenschutzerklärung aktualisieren</p>
                         </div>
                     </div>
                     <form class=\"row form\" action=\"/?view=privacypolicy&action=update\" method=\"post\">
                         <div class=\"col-12\">
                             <div class=\"container-fluid p-0\">
                                 <div class=\"row mb-3\">
                                     <div class=\"col-12\">
                                         <label class=\"form-label$privacyPolicyLabelBSClass\" for=\"privacyPolicy\">Datenschutzerklärung (max. $privacyPolicyMaxLength Zeichen):</label>
                                         <textarea placeholder=\"Datenschutzerklärung hier eingeben ...\" class=\"w-100 h-100 form-control$privacyPolicyInputBSClass\" id=\"privacyPolicy\" name=\"privacyPolicy\" maxlength=\"$privacyPolicyMaxLength\">{$privacyPolicyHTMLSafe}</textarea>
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
                                        <a class=\"w-100 btn btn-secondary\" href=\"/?view=privacypolicy\">Zurück</a>
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
                $privacyPolicyContentHTML =
                "
                <div class=\"container-fluid p-0\">
                    <div class=\"row mt-3 mb-5\">
                        <div class=\"col-2\" style=\"background-image: url('/img/symbol/shield-fill-check-success.svg'); background-size: contain; background-repeat: no-repeat; background-position: center;\"></div>
                        <div class=\"col-10\">
                           <span class=\"fs-2 fw-bold text-success\">Datenschutzerklärung erfolgreich aktualisiert!</span>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-success w-100\" href=\"/?view=privacypolicy&action=update\">Datenschutzerklärung weiter bearbeiten</a>
                        </div>
                    </div>
                    <div class=\"row mb-3\">
                        <div class=\"col-12\">
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=privacypolicy\">Zur Datenschutzerklärung</a>
                        </div>
                    </div>
                </div>
                ";
            }
            else
            {
                // Invalid request
                $privacyPolicyContentHTML =
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
                            <a class=\"btn btn-secondary w-100\" href=\"/?view=privacypolicy\">Zur Datenschutzerklärung</a>
                        </div>
                    </div>
                </div>
                ";
            }
        }

        $feedHTML = $this->globalView->genFeed(null, $privacyPolicyContentHTML);

        $this->globalView->show($companyData, $feedHTML);
    }
}