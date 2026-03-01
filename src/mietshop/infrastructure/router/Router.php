<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IErrorView;
use src\mietshop\infrastructure\database\MySQLAdapter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\CategoryModel;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\ErrorPresenter;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\presenter\ImprintPresenter;
use src\mietshop\presenter\PrivacyPolicyPresenter;
use src\mietshop\presenter\TermsOfServicePresenter;
use src\mietshop\view\ErrorView;
use src\mietshop\view\GlobalView;
use src\mietshop\view\HomeView;
use src\mietshop\view\ImprintView;
use src\mietshop\view\PrivacyPolicyView;
use src\mietshop\view\TermsOfServiceView;

class Router implements IRouter
{
    private function chooseErrorPresenter(IErrorView $errorView, ICompanyModel $companyModel, HTTPRequest $request) : void
    {
        $errorPresenter = new ErrorPresenter($errorView, $companyModel);
        $errorPresenter->process($request);
    }

    public function route(IHTTPRequest $request) : void
    {
        $databaseAdapter = new MySQLAdapter();
        $databaseAdapter->connect("localhost", "myWebsite", "root", "myPassword");
        $companyModel = new CompanyModel($databaseAdapter);
        $categoryModel = new CategoryModel($databaseAdapter);
        $globalView = new GlobalView();
        $errorView = new ErrorView($globalView);

        if(sizeof($request->getQueryKeys()) === 0)
        {
            $homeView = new HomeView($globalView);
            $homePresenter = new HomePresenter($companyModel, $categoryModel, $homeView, $errorView);
            $homePresenter->process($request);
        }
        else
        {
            if(in_array("view", $request->getQueryKeys()))
            {
                $viewValue = $request->getQueryValue("view");

                if(strcmp($viewValue, "imprint") === 0)
                {
                    $imprintView = new ImprintView($globalView);
                    $imprintPresenter = new ImprintPresenter($errorView, $imprintView, $companyModel);
                    $imprintPresenter->process();
                }
                else if(strcmp($viewValue, "termsofservice") === 0)
                {
                    $termsOfServiceView = new TermsOfServiceView($globalView);
                    $termsOfServicePresenter = new TermsOfServicePresenter($errorView, $termsOfServiceView, $companyModel);
                    $termsOfServicePresenter->process();
                }
                else if(strcmp($viewValue, "privacypolicy") === 0)
                {
                    $privacyPolicyView = new PrivacyPolicyView($globalView);
                    $privacyPolicyPresenter = new PrivacyPolicyPresenter($privacyPolicyView, $errorView, $companyModel);
                    $privacyPolicyPresenter->process();
                }
                else
                {
                    $this->chooseErrorPresenter($errorView, $companyModel, $request);
                }
            }
            else
            {
                $this->chooseErrorPresenter($errorView, $companyModel, $request);
            }
        }
    }
}