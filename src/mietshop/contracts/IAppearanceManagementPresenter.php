<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\http\IHTTPRequest;

interface IAppearanceManagementPresenter
{
    public function process(IHTTPRequest $request, array $sessionValues): void;
}