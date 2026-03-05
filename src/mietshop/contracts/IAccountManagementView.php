<?php

namespace src\mietshop\contracts;

interface IAccountManagementView
{
    /**
     * @param array $companyData
     * @param array $accountResults
     * @return void
     */
    public function show(array $companyData, array $accountResults) : void;
}