<?php

namespace src\mietshop\contracts;

interface IAccountManagementView
{
    /**
     * Shows the corresponding account management features or a specific action feature you can perform
     * (e.g. show all possible functions, show the page to create an account ["create" passed for $action parameter], read all accounts ["read"], update a permission level of an account ["update"] or delete an account ["delete"].
     *
     * @param array $companyData The company data array as described in ICompanyData
     * @param array $accountResults Account results array as described in IAccountModel (only needed if you pass the value "read" for parameter $action - otherwise pass an empty array here for other actions!)
     * @param string $action The CRUD action value or the value "all" which allows you to see the desired form or data (whether "all" [see a summary of all CRUD features], "create" [create an account], "read" [read all accounts - depending on the page you want to look at], "update" [update a permission level of an account] or "delete" [delete an account])
     * @param int $pageIndex Optional: only needed if you choose the action value "read"
     * @param int $pagesAmount Optional: amount as described in IAccountModel. Only needed together with parameter $page if you choose the action value "read"
     * @return void
     */
    public function show(array $companyData, array $accountResults, string $action, int $pageIndex = 0, int $pagesAmount = 0) : void;
}