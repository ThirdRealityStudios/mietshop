<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\ITermsOfServicePresenter;
use src\mietshop\contracts\ITermsOfServiceView;

class TermsOfServicePresenter implements ITermsOfServicePresenter
{
    private IErrorView $errorView;
    private ITermsOfServiceView $termsOfServiceView;
    private ICompanyModel $companyModel;

    public function __construct(IErrorView $errorView, ITermsOfServiceView $termsOfServiceView, ICompanyModel $companyModel)
    {
        $this->errorView = $errorView;
        $this->termsOfServiceView = $termsOfServiceView;
        $this->companyModel = $companyModel;
    }

    public function process()
    {
        $companyData = $this->companyModel->getCompanyData();

        if(!empty($companyData) && !empty($companyData["TermsOfService"][0]))
        {
            $this->termsOfServiceView->show($companyData);
        }
        else if(!empty($companyData) && empty($companyData["TermsOfService"][0]))
        {
            $this->errorView->show([], "Fehler","Es scheint so, als ob die AGBs noch nicht festgelegt worden sind.");
        }
        else
        {
            $this->errorView->show([], "Internal Loading Error","Could not fetch terms of service of the website. Please visit us again a little later. We are working on it.");
        }
    }
}