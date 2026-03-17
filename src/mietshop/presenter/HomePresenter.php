<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICategoryModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\contracts\IHomeView;
use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\IHTTPRequest;

class HomePresenter implements IPresenter
{
    private ICompanyModel $companyModel;
    private ICategoryModel $categoryModel;
    private IHomeView $homeView;
    private IErrorView $errorView;

    public function __construct(ICompanyModel $companyModel, ICategoryModel $categoryModel, IHomeView $homeView, IErrorView $errorView)
    {
        $this->companyModel = $companyModel;
        $this->categoryModel = $categoryModel;
        $this->homeView = $homeView;
        $this->errorView = $errorView;
    }

    public function process(IHTTPRequest $request): void
    {
        $companyData = $this->companyModel->getCompanyData();
        $categories = $this->categoryModel->readAllCategories();

        if(empty($companyData))
        {
            $this->errorView->show([], "Internal Loading Error", "Could not fetch basic information about the website. Please visit us again a little later. We are working on it.");

            return;
        }

        $this->homeView->show($companyData, $categories);
    }
}