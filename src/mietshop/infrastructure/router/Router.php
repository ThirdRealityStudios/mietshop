<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\infrastructure\database\MySQLAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\view\GlobalView;
use src\mietshop\view\HomeView;

class Router implements IRouter
{
    public function route(IHTTPRequest $request) : void
    {
        $databaseAdapter = new MySQLAdapter();
        $databaseAdapter->connect("localhost", "myWebsite", "root", "myPassword");
        $companyModel = new CompanyModel($databaseAdapter);
        $globalView = new GlobalView();

        if(sizeof($request->getQueryKeys()) === 0)
        {
            $homeView = new HomeView($globalView);
            $homePresenter = new HomePresenter($companyModel, $homeView);
            $homePresenter->process($request);
        }
        else if(sizeof($request->getQueryKeys()) > 0)
        {
            echo "Other page";
        }
    }
}