<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\http\IHTTPRequest;

interface IImprintPresenter
{
    public function process(IHTTPRequest &$httpRequest, array &$sessionValues): void;
}