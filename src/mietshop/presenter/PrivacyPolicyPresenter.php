<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IPrivacyPolicyPresenter;
use src\mietshop\contracts\IPrivacyPolicyView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class PrivacyPolicyPresenter implements IPrivacyPolicyPresenter
{
    private IPrivacyPolicyView $privacyPolicyView;
    private ICompanyModel $companyModel;
    private IErrorView $errorView;
    private IAccountModel $accountModel;

    public function __construct(IPrivacyPolicyView $privacyPolicyView, IErrorView $errorView, ICompanyModel $companyModel, IAccountModel $accountModel)
    {
        $this->privacyPolicyView = $privacyPolicyView;
        $this->companyModel = $companyModel;
        $this->errorView = $errorView;
        $this->accountModel = $accountModel;
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues)
    {
        $companyData = $this->companyModel->getCompanyData();

        if(empty($companyData))
        {
            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen Sie es später erneut.");
        }
        else
        {
            $canUpdatePrivacyPolicyPage = isset($sessionValues["Email"]) && $this->accountModel->isSupervisor($sessionValues["Email"]);

            if($httpRequest->isRequestParameterMatching("action", "update"))
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
                            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
                        }
                        else
                        {
                            $this->privacyPolicyView->show($companyData, false, "update", $updateResponseCode ? 1 : -1);
                        }
                    }
                    else // Does the user want to see the submit form?
                    {
                        $this->privacyPolicyView->show($companyData, false, "update");
                    }
                }
                else
                {
                    // Is no supervisor or not logged in. Just show the normal visitor privacy policy page.
                    $this->privacyPolicyView->show($companyData, false);
                }
            }
            else if($canUpdatePrivacyPolicyPage) // Else just watch the privacy policy page (visitor with supervisor rights - usually owner and admin - so he can use the edit button)
            {
                $this->privacyPolicyView->show($companyData, true);
            }
            else // Else just a visitor with no rights at all or insufficient permissions.
            {
                $this->privacyPolicyView->show($companyData, false);
            }
        }
    }
}