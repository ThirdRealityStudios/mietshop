<?php

namespace src\mietshop\contracts;

interface IImprintView
{
    /**
     * @param array $companyData
     * @return void
     */
    public function show(array $companyData) : void;
}