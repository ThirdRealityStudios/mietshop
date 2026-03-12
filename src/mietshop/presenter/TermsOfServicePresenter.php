<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\ILegalView;
use src\mietshop\contracts\ITermsOfServicePresenter;
use src\mietshop\infrastructure\http\IHTTPRequest;

class TermsOfServicePresenter implements ITermsOfServicePresenter
{
    private ILegalView $legalView;
    private IErrorView $errorView;
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;

    public function __construct(ILegalView $legalView, IErrorView $errorView, ICompanyModel $companyModel, IAccountModel $accountModel)
    {
        $this->legalView = $legalView;
        $this->errorView = $errorView;
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues) : void
    {
        $companyData = $this->companyModel->getCompanyData();

        if(empty($companyData))
        {
            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen Sie es später erneut.");
        }
        else
        {
            $canUpdateTermsOfServicePage = isset($sessionValues["Email"]) && $this->accountModel->isSupervisor($sessionValues["Email"]);

            if($httpRequest->isRequestParameterMatching("action", "update"))
            {
                // Does an account session exist and is the account a registered supervisor? (maybe the session is older and account was deleted e.g. by an admin)
                if($canUpdateTermsOfServicePage)
                {
                    $possiblePostTermsOfServiceValue = $httpRequest->getPOSTValue("termsOfService");

                    // Does the user want to update the terms of service after form submission?
                    if(is_string($possiblePostTermsOfServiceValue))
                    {
                        // There might be an invalid field submitted (usually only if more characters were transmitted than allowed - possible manipulation of the POST request).
                        $updateResponseCode = $this->companyModel->setTermsOfService($possiblePostTermsOfServiceValue);

                        // Get the updated company data.
                        $companyData = $this->companyModel->getCompanyData();

                        if(empty($companyData))
                        {
                            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
                        }
                        else
                        {
                            $this->legalView->show($companyData, false, false, "update", $updateResponseCode ? 1 : -1);
                        }
                    }
                    else // Does the user want to see the submit form?
                    {
                        $this->legalView->show($companyData, false, false, "update");
                    }
                }
                else
                {
                    // Is no supervisor or not logged in. Just show the normal visitor terms of service page.
                    $this->legalView->show($companyData, false, false);
                }
            }
            else if($canUpdateTermsOfServicePage) // Else just watch the terms of service page (visitor with supervisor rights - usually owner and admin - so he can use the edit button)
            {
                $this->legalView->show($companyData, true, false);
            }
            else // Else just a visitor with no rights at all or insufficient permissions.
            {
                $this->legalView->show($companyData, false, false);
            }
        }
    }
}