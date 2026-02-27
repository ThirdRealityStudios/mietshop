<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IView;

class HomeView implements IView
{
    public function show(array $data = []): void
    {
        echo "HomeView<br>Company name character limit = {$data["CompanyNameCharacterLimit"]}";
    }
}