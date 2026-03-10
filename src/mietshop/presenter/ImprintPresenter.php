<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IImprintPresenter;
use src\mietshop\contracts\IImprintView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class ImprintPresenter implements IImprintPresenter
{
    private IErrorView $errorView;
    private IImprintView $imprintView;
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;

    public function __construct(IErrorView $errorView, IImprintView $imprintView, ICompanyModel $companyModel, IAccountModel $accountModel)
    {
        $this->errorView = $errorView;
        $this->imprintView = $imprintView;
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
    }

    /**
     * Checks if every form input was sent (except the checkboxes - because checkbox values are only sent via POST parameter when they are selected)
     *
     * @param IHTTPRequest $httpRequest
     * @return bool
     */
    private function isEveryPostParameterSent(IHTTPRequest &$httpRequest) : bool
    {
        $postCompanyName = $httpRequest->getPOSTValue("companyName");
        $postStreet = $httpRequest->getPOSTValue("street");
        $postZipCode = $httpRequest->getPOSTValue("zipCode");
        $postPlace = $httpRequest->getPOSTValue("place");
        $postEmail = $httpRequest->getPOSTValue("email");
        $postPhoneMobile = $httpRequest->getPOSTValue("phoneMobile");
        $postPhoneNormal = $httpRequest->getPOSTValue("phoneNormal");
        $postOwner = $httpRequest->getPOSTValue("owner");

        return is_string($postCompanyName)
            && is_string($postStreet)
            && is_string($postZipCode)
            && is_string($postPlace)
            && is_string($postEmail)
            && is_string($postPhoneMobile)
            && is_string($postPhoneNormal)
            && is_string($postOwner);
    }

    private function uploadImprintForm(IHTTPRequest &$httpRequest) : int
    {
        $updateCodes = [];

        $updateCodes[] = $this->companyModel->setCompanyName($httpRequest->getPOSTValue("companyName")) ? 1 : -1;
        $updateCodes[] = $this->companyModel->setStreet($httpRequest->getPOSTValue("street")) ? 2 : -2;
        $updateCodes[] = $this->companyModel->setZipCode($httpRequest->getPOSTValue("zipCode")) ? 3 : -3;
        $updateCodes[] = $this->companyModel->setPlace($httpRequest->getPOSTValue("place")) ? 4 : -4;
        $updateCodes[] = $this->companyModel->setEmail($httpRequest->getPOSTValue("email")) ? 5 : -5;
        $updateCodes[] = $this->companyModel->setPhoneMobile($httpRequest->getPOSTValue("phoneMobile")) ? 6 : -6;
        $updateCodes[] = $this->companyModel->setPhoneNormal($httpRequest->getPOSTValue("phoneNormal")) ? 7 : -7;
        $updateCodes[] = $this->companyModel->setOwner($httpRequest->getPOSTValue("owner")) ? 8 : -8;

        $updateCodes[] = $this->companyModel->setP19UStGApplies(!is_null($httpRequest->getPOSTValue("p19UStGApplies"))) ? 9 : -9; // checkbox is checked in form = it was sent via POST request
        $updateCodes[] = $this->companyModel->setP36VSBGApplies(!is_null($httpRequest->getPOSTValue("p36VSBGApplies"))) ? 10 : -10;

        foreach($updateCodes as $updateCode)
        {
            if($updateCode < 0)
            {
                // Return the first invalid field submitted
                return $updateCode;
            }
        }

        return 0; // No invalid field submitted
    }

    public function process(IHTTPRequest &$httpRequest, array &$sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();

        if(empty($companyData))
        {
            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
        }
        else // Should be the usual case
        {
            $canUpdateImprintPage = isset($sessionValues["Email"]) && $this->accountModel->isSupervisor($sessionValues["Email"]);

            if($httpRequest->isRequestParameterMatching("action", "update"))
            {
                // Does an account session exist and is the account a registered supervisor? (maybe the session is older and account was deleted e.g. by an admin)
                if($canUpdateImprintPage)
                {
                    // Does the user want to update the imprint only after form submission?
                    if($this->isEveryPostParameterSent($httpRequest))
                    {
                        // There might be an invalid field submitted (only the first field interesting - but if not then 0 is calculated here).
                        $firstInvalidField = $this->uploadImprintForm($httpRequest);

                        // Get the updated company data.
                        $companyData = $this->companyModel->getCompanyData();

                        if(empty($companyData))
                        {
                            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
                        }
                        else
                        {
                            $this->imprintView->show($companyData, false, "update", $firstInvalidField);
                        }
                    }
                    else // Does the user want to see the submit form?
                    {
                        $this->imprintView->show($companyData, false, "update");
                    }
                }
                else
                {
                    // Is no supervisor or not logged in. Just show the normal visitor imprint page.
                    $this->imprintView->show($companyData, false);
                }
            }
            else if($canUpdateImprintPage) // Else just watch the imprint page (visitor with supervisor rights - usually owner and admin - so he can use the edit button)
            {
                $this->imprintView->show($companyData, true);
            }
            else // Else just a visitor with no rights at all or insufficient permissions.
            {
                $this->imprintView->show($companyData, false);
            }
        }
    }
}