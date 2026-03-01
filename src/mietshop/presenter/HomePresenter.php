<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICategoryModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IHomeView;
use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private ICompanyModel $companyModel;
    private ICategoryModel $categoryModel;
    private HomeView $view;

    public function __construct(ICompanyModel $companyModel, ICategoryModel $categoryModel, IHomeView $view)
    {
        $this->companyModel = $companyModel;
        $this->categoryModel = $categoryModel;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $host = $request->getHeaderValue("host");

        if(is_string($host) && strlen($host) > 0)
        {
            // $this->companyModel->setCompanyName("Sehr cooles Unternehmen GmbH");
            // $this->companyModel->setOwner("Gutfried Alman");

            // $this->companyModel->setStreet("Kurwa Mac Str. 3a");

            $companyData = $this->companyModel->getCompanyData();
            $categories = $this->categoryModel->readAllCategories();

            // var_dump($companyData);

            if(empty($companyData))
            {
                return;
            }

            $this->view->show($host, $companyData, $categories);
        }
    }
}