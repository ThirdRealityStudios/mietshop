<?php

namespace src\mietshop\contracts;

interface IErrorView
{
    public function show(array $companyData, string $errorFeedTitle, string $errorFeedContent) : void;
}