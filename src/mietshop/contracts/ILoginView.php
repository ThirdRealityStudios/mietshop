<?php

namespace src\mietshop\contracts;

interface ILoginView
{
    public function show(array $companyData) : void;
}