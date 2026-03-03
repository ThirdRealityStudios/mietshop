<?php

namespace src\mietshop\contracts;

interface IAccountView
{
    /**
     * @param array $companyData
     * @param array $accountData
     * @return mixed
     */
    public function show(array $companyData, array $accountData);
}