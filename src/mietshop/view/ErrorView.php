<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;

class ErrorView
{
    private $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show()
    {

    }
}