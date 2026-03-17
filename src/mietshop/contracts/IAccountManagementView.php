<?php

namespace src\mietshop\contracts;

interface IAccountManagementView
{
    /**
     * Shows the corresponding account management features or a specific action feature you can perform
     * (e.g. show all possible functions, show the page to create an account ["create" passed for $action parameter], read all accounts ["read"], update a permission level of an account ["update"] or delete an account ["delete"].
     *
     * @param array $companyData The company data array as described in ICompanyData
     * @param array $appearanceData By the AppearanceModel
     * @param array $accountResults Account results array as described in IAccountModel (only needed if you pass the value "read" for parameter $action - otherwise pass an empty array here for other actions!)
     * @param array $permissionRoles Optional: only needed if you choose the value "create" or "update" for parameter $action. Expected associative array like: [ 0 => "Guest", 1 => "Moderator", 2 => "Supporter", 3 => "Owner", 4 => "Admin" ]. If not needed, pass an empty array!
     * @param string $action The CRUD action value or the value "all" which allows you to see the desired form or data (whether "all" [see a summary of all CRUD features], "create" [create an account], "read" [read all accounts - depending on the page you want to look at], "update" [update a permission level of an account] or "delete" [delete an account])
     * @param int $messageValue Optional: only needed if you use the request parameter "action" which has the value "create", "update" or "delete". Used to show which form input was incorrect after submit or if the action was successful after submit. A value of 0 is usually interpreted as "ignore message value" / neutral.
     * @param int $pageIndex Optional: only needed if you choose the action value "read". If not needed, then leave out.
     * @param int $pagesAmount Optional: amount as described in IAccountModel. Only needed together with parameter $page if you choose the action value "read". If not needed, leave out together with parameter $pageIndex.
     * @return void
     */
    public function show(array $companyData, array $appearanceData, array $accountResults, array $permissionRoles, string $action, int $messageValue = 0, int $pageIndex = 0, int $pagesAmount = 0) : void;
}