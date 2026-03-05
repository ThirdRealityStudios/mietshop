<?php

namespace src\mietshop\contracts;

interface IAccountView
{
    /**
     * Shows the account page,
     * depending on the specific user permissions and more.
     *
     * The expected $accountData array should look like:
     *
     * [
     *  "Email" => "example@mail.com",
     *  "PermissionRole" => "Owner",
     *  "CanManageAccounts" => false
     * ]
     *
     * @param array $companyData Expected as described in ICompanyModel
     * @param array $accountData Expected something like described above.
     * @return void
     */
    public function show(array $companyData, array $accountData) : void;
}