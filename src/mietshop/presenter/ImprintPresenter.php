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

    private function isEveryPostParameterSent(IHTTPRequest &$httpRequest) : bool
    {
        return is_string($httpRequest->getPOSTValue(""));
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
            if($httpRequest->isRequestParameterMatching("action", "update"))
            {
                // Does an account session exist and is the account a registered supervisor? (maybe the session is older and account was deleted e.g. by an admin)
                if(isset($sessionValues["Email"]) && $this->accountModel->isSupervisor($sessionValues["Email"]))
                {
                    // Does the user want to update the imprint only after form submission?
                    if($this->isEveryPostParameterSent($httpRequest))
                    {
                        $messageValue = -1; // $this->companyModel->set...(...);

                        $this->imprintView->show($companyData, "update", $messageValue);
                    }
                    else // Does the user want to see the submit form?
                    {
                        $this->imprintView->show($companyData, "update");
                    }
                }
                else
                {
                    // Is no supervisor or not logged in. Just show the normal visitor imprint page.
                    $this->imprintView->show($companyData);
                }
            }
            else // Else just watch the imprint page (normal visitor)
            {
                $this->imprintView->show($companyData);
            }
        }
    }
}