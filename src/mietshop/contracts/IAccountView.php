<?php

namespace src\mietshop\contracts;

interface IAccountView
{
    /**
     * @param array $companyData Expected as described in ICompanyModel
     * @param array $accountData Expected something like [ "Email" => "example@mail.com", "PermissionRole" = "Owner" ]
     * @return void
     */
    public function show(array $companyData, array $accountData) : void;
}