<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\contracts\IGlobalModel;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private IGlobalModel $model;
    private HomeView $view;

    public function __construct(IGlobalModel $model, HomeView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $host = $request->getHeaderValue("host");

        if(is_string($host))
        {
            $companyNameCharacterLimit = $this->model->getCompanyModel()->getNameCharacterLimit();

            $this->view->show([
                "host" => $request->getHeaderValue("host"),
                "CompanyNameCharacterLimit" => $companyNameCharacterLimit]);

            /*
            foreach($request -> getHeaderKeys() as $key)
            {
                echo "$key: ";

                $value = $request -> getHeaderValue($key);


                echo "$value<br>";
            }
            */
        }
    }
}