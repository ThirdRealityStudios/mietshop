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
     * @param array $appearanceData
     * @param string $email The email of the account
     * @param bool $isSupervisor Is the user supervisor?
     * @param string $permissionRoleName The name of the permission role in full text
     * @param string $welcomeMessage A very short welcome message, e.g. "Good Morning, Sir!" (depending on the time)
     * @return void
     */
    public function show(array $companyData, array $appearanceData, string $email, bool $isSupervisor, string $permissionRoleName, string $welcomeMessage) : void;
}