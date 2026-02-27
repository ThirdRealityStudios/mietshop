<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IView;

class HomeView implements IView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $data = []): void
    {
        echo "HomeView<br>Company name character limit = {$data["CompanyNameCharacterLimit"]}";
    }
}