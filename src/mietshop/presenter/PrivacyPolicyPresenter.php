<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\ILegalView;
use src\mietshop\contracts\IPrivacyPolicyPresenter;
use src\mietshop\infrastructure\http\IHTTPRequest;

class PrivacyPolicyPresenter implements IPrivacyPolicyPresenter
{
    private ILegalView $legalView;
    private IErrorView $errorView;

    private ICompanyModel $companyModel;
    private IAppearanceModel $appearanceModel;
    private IAccountModel $accountModel;

    public function __construct(ILegalView $legalView, IErrorView $errorView, ICompanyModel $companyModel, IAppearanceModel $appearanceModel, IAccountModel $accountModel)
    {
        $this->legalView = $legalView;
        $this->errorView = $errorView;

        $this->companyModel = $companyModel;
        $this->appearanceModel = $appearanceModel;
        $this->accountModel = $accountModel;
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues)
    {
        $companyData = $this->companyModel->getCompanyData();
        $appearanceData = $this->appearanceModel->getAppearanceData();

        if(empty($companyData))
        {
            $this->errorView->show($companyData, $appearanceData, "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen Sie es später erneut.");
        }
        else
        {
            $canUpdatePrivacyPolicyPage = isset($sessionValues["Email"]) && $this->accountModel->isSupervisor($sessionValues["Email"]);

            if($httpRequest->isParameterMatching("action", "update"))
            {
                // Does an account session exist and is the account a registered supervisor? (maybe the session is older and account was deleted e.g. by an admin)
                if($canUpdatePrivacyPolicyPage)
                {
                    $possiblePostPrivacyPolicyValue = $httpRequest->getPOSTValue("privacyPolicy");

                    // Does the user want to update the privacy policy after form submission?
                    if(is_string($possiblePostPrivacyPolicyValue))
                    {
                        // There might be an invalid field submitted (usually only if more characters were transmitted than allowed - possible manipulation of the POST request).
                        $updateResponseCode = $this->companyModel->setPrivacyPolicy($possiblePostPrivacyPolicyValue);

                        // Get the updated company data.
                        $companyData = $this->companyModel->getCompanyData();

                        if(empty($companyData))
                        {
                            $this->errorView->show($companyData, $appearanceData, "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
                        }
                        else
                        {
                            $this->legalView->show($companyData, $appearanceData, false, true, "update", $updateResponseCode ? 1 : -1);
                        }
                    }
                    else // Does the user want to see the submit form?
                    {
                        $this->legalView->show($companyData, $appearanceData, false, true, "update");
                    }
                }
                else
                {
                    // Is no supervisor or not logged in. Just show the normal visitor privacy policy page.
                    $this->legalView->show($companyData, $appearanceData, false, true);
                }
            }
            else if($canUpdatePrivacyPolicyPage) // Else just watch the privacy policy page (visitor with supervisor rights - usually owner and admin - so he can use the edit button)
            {
                $this->legalView->show($companyData, $appearanceData, true, true);
            }
            else // Else just a visitor with no rights at all or insufficient permissions.
            {
                $this->legalView->show($companyData, $appearanceData, false, true);
            }
        }
    }
}