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

    private function showInvalidRequest(array $companyData)
    {
        $this->errorView->show($companyData, "Ungültige Anfrage", "Aktualisierung fehlgeschlagen.");
    }

    private function genForm(array $companyData, string $target, string $element) : string
    {
        if(strcmp($target, "color") === 0)
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
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <label class=\"form-label fw-bold text-body\" for=\"body\">Body:</label>
                                    <input type=\"radio\" id=\"body\" name=\"color\" class=\"w-100 btn btn-primary\" value=\"body\">
                                </div>
                            </div>
                            <div class=\"row mb-3\">
                                <div class=\"col-12\">
                                    <label class=\"form-label fw-bold text-bg\" for=\"body\">Secondary:</label>
                                    <input type=\"radio\" id=\"body\" name=\"color\" class=\"w-100 btn btn-primary\" value=\"body\">
                                </div>
                            </div>
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
        else if(strcmp($target, "image") === 0)
        {
            return
            "
            
            ";
        }
        else
        {
            $this->showInvalidRequest($companyData);
        }
    }

    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0): void
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
                <div class=\"container-fluid p-0\">
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
                    if($messageValue === 0)
                    {
                        // Show normal update form
                        $feedContentHTML = $this->genForm($companyData, "color", $element);
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