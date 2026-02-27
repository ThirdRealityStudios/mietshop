<?php

namespace src\mietshop\model;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IGlobalModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class GlobalModel implements IGlobalModel
{
    private ICompanyModel $companyModel;
    private IDatabaseAdapter $databaseAdapter;

    public function __construct(ICompanyModel $companyModel, IDatabaseAdapter $databaseAdapter)
    {
        $this->companyModel = $companyModel;
        $this->databaseAdapter = $databaseAdapter;
    }

    public function getCompanyModel(): ICompanyModel
    {
        return $this->companyModel;
    }
}