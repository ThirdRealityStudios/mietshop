<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\model\CompanyModel;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private CompanyModel $model;
    private HomeView $view;

    public function __construct(CompanyModel $model, HomeView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $companyNameCharLimit = $this->model->getNameCharacterLimit();

        $this->view->show(["CompanyNameCharacterLimit" => $companyNameCharLimit]);

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