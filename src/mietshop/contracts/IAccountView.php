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
     * @param string $email The email of the account
     * @param bool $isSupervisor Is the user supervisor?
     * @param string $permissionRoleName The name of the permission role in full text
     * @return void
     */
    public function show(array $companyData, string $email, bool $isSupervisor, string $permissionRoleName) : void;
}