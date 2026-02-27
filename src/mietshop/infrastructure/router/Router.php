<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\contracts\CompanyRepository;
use src\mietshop\infrastructure\database\MySQLAdapter;
use src\mietshop\infrastructure\http\IHTTPRequest;
use src\mietshop\model\CompanyModel;
use src\mietshop\presenter\HomePresenter;
use src\mietshop\view\HomeView;

class Router implements IRouter
{
    public function route(IHTTPRequest $request) : void
    {
        $host = $request->getHeaderValue("host");

        /* Make sure no one can send manipulated HTTP requests where "host" is not an IP/domain
         * but a malicious string to change the behavior of the connect() function. */
        if(is_string($host) && filter_var(gethostbyname($host), FILTER_VALIDATE_IP))
        {
            $databaseAdapter = new MySQLAdapter();
            $databaseAdapter->connect($host, "myWebsite", "root", "myPassword");
            $globalModel = new CompanyModel($databaseAdapter);

            if(sizeof($request->getQueryKeys()) === 0)
            {
                $homeView = new HomeView();
                $homePresenter = new HomePresenter($globalModel, $homeView);
                $homePresenter->process($request);
            }
            else
            {

            }
        }
    }
}