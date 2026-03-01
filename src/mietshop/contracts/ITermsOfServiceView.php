<?php

namespace src\mietshop\contracts;

interface ITermsOfServiceView
{
    public function show(array $companyData) : void;
}