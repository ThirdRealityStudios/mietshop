<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IAppearanceManagementView;
use src\mietshop\contracts\IGlobalView;

class AppearanceManagementView implements IAppearanceManagementView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0): void
    {

    }
}