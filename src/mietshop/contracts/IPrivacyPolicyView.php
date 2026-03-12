<?php

namespace src\mietshop\contracts;

interface IPrivacyPolicyView
{
    public function show(array $companyData, bool $showEditButton, string $actionValue = "", int $messageValue = 0) : void;
}