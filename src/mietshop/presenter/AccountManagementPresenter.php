<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IAccountManagementPresenter;
use src\mietshop\contracts\IAccountManagementView;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\http\IHTTPRequest;

class AccountManagementPresenter implements IAccountManagementPresenter
{
    private ICompanyModel $companyModel;
    private IAccountModel $accountModel;
    private IAccountManagementView $accountManagementView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, IAccountModel $accountModel, IAccountManagementView $accountManagementView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->accountModel = $accountModel;
        $this->accountManagementView = $accountManagementView;
        $this->errorView = $errorView;
    }

    public function process(IHTTPRequest $request, array $sessionValues): void
    {
        $companyData = $this->companyModel->getCompanyData();

        $accountResults = $this->accountModel->readAccounts(0);

        $this->accountManagementView->show($companyData, $accountResults);
    }
}