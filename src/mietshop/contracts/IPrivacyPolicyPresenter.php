<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\http\IHTTPRequest;

interface IPrivacyPolicyPresenter
{
    public function process(IHTTPRequest &$httpRequest, array &$sessionValues);
}