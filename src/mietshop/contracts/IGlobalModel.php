<?php

namespace src\mietshop\contracts;

interface IGlobalModel
{
    public function getCompanyModel(): ICompanyModel;
}