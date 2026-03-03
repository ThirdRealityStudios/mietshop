<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IHomeView;
use src\mietshop\contracts\ILoginPresenter;
use src\mietshop\contracts\ILoginView;

class LoginPresenter implements ILoginPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private ILoginView $loginView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, ILoginView $loginView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->loginView = $loginView;
        $this->errorView = $errorView;
    }

    public function process(): void
    {
        $companyData = $this->companyModel->getCompanyData();

        if(!empty($companyData))
        {
            $this->loginView->show($companyData);
        }
        else
        {
            $this->errorView->show([], "Dienst nicht erreichbar", "Bitte versuchen Sie es später erneut.");
        }
    }
}