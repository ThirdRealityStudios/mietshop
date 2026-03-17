<?php

namespace src\mietshop\contracts;

interface IErrorView
{
    public function show(array $companyData, array $appearanceData, string $errorFeedTitle, string $errorFeedContent) : void;
}