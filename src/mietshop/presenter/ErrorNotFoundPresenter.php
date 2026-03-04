<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\IHTTPRequest;

/**
 * Decides error message should be displayed,
 * using an IErrorView.
 */
class ErrorNotFoundPresenter implements IPresenter
{
    private IErrorView $errorView;
    private ICompanyModel $companyModel;

    public function __construct(IErrorView $errorView, ICompanyModel $companyModel)
    {
        $this->errorView = $errorView;
        $this->companyModel = $companyModel;
    }

    public function process(IHTTPRequest $request): void
    {
        $this->errorView->show($this->companyModel->getCompanyData(), "Seite existiert nicht", "Entweder haben Sie sich vertippt oder derzeit gibt es technische Probleme. Bitte versuchen Sie es später erneut.");
    }
}