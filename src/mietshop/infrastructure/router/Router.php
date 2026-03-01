<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\infrastructure\database\MySQLAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\CategoryModel;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\ErrorPresenter;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\view\ErrorView;
use src\mietshop\view\GlobalView;
use src\mietshop\view\HomeView;

class Router implements IRouter
{
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
            $errorPresenter = new ErrorPresenter($errorView, $companyModel);
            $errorPresenter->process($request);
        }
    }
}