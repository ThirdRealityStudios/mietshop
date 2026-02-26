<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\model\GlobalModel;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private GlobalModel $model;
    private HomeView $view;

    public function __construct(GlobalModel $model, HomeView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $this->view->show();

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