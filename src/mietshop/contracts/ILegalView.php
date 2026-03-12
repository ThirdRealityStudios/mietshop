<?php

namespace src\mietshop\contracts;

interface ILegalView
{
    /**
     * @param array $companyData
     * @param bool $showEditButton
     * @param bool $isPrivacyPolicy Choose 'true' if you want to visualize the privacy policy. Choose 'false', if you want to visualize the terms of service.
     * @param string $actionValue
     * @param int $messageValue Specify 1 if the terms of service or privacy policy could be updated successfully. Choose -1 if it failed (indicate error). Choose 0 if you only want to visualize the terms of service / privacy policy - whether the update form or the text information page.
     * @return void
     */
    public function show(array $companyData, bool $showEditButton, bool $isPrivacyPolicy, string $actionValue = "", int $messageValue = 0) : void;
}