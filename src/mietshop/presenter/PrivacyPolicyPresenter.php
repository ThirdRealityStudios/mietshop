<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IPrivacyPolicyPresenter;
use src\mietshop\contracts\IPrivacyPolicyView;

class PrivacyPolicyPresenter implements IPrivacyPolicyPresenter
{
    private IPrivacyPolicyView $privacyPolicyView;
    private ICompanyModel $companyModel;
    private IErrorView $errorView;

    public function __construct(IPrivacyPolicyView $privacyPolicyView, IErrorView $errorView, ICompanyModel $companyModel)
    {
        $this->privacyPolicyView = $privacyPolicyView;
        $this->companyModel = $companyModel;
        $this->errorView = $errorView;
    }

    public function process()
    {
        $companyData = $this->companyModel->getCompanyData();

        if(!empty($companyData) && !empty($companyData["PrivacyPolicy"][0]))
        {
            $this->privacyPolicyView->show($companyData);
        }
        else if(!empty($companyData) && empty($companyData["PrivacyPolicy"][0]))
        {
            $this->errorView->show([], "Fehler","Es scheint so, als ob die Datenschutzerklärung noch nicht festgelegt worden ist.");
        }
        else
        {
            $this->errorView->show([], "Internal Loading Error", "Could not fetch privacy policy of the website. Please visit us again a little later. We are working on it.");
        }
    }
}