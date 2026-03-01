<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IImprintPresenter;
use src\mietshop\contracts\IImprintView;

class ImprintPresenter implements IImprintPresenter
{
    private IErrorView $errorView;
    private IImprintView $imprintView;
    private ICompanyModel $companyModel;

    public function __construct(IErrorView $errorView, IImprintView $imprintView, ICompanyModel $companyModel)
    {
        $this->errorView = $errorView;
        $this->imprintView = $imprintView;
        $this->companyModel = $companyModel;
    }

    public function process(): void
    {
        $companyData = $this->companyModel->getCompanyData();

        if(empty($companyData))
        {
            $this->errorView->show([], "Abfragefehler", "Derzeit scheint es einen technischen Fehler mit der Datenabfrage im System zu geben. Bitte versuchen es später erneut.");
        }
        else
        {
            $this->imprintView->show($companyData);
        }
    }
}