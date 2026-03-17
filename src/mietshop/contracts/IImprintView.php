<?php

namespace src\mietshop\contracts;

interface IImprintView
{
    /**
     * @param array $companyData
     * @param array $appearanceData
     * @param bool $showEditButton A must. Specify if the edit button should be shown to edit the imprint (usually for specific registered accounts with the corresponding rights who watch the page).
     * @param string $actionValue Optional: only needed if you use the request parameter "action" and if it has the value "update". Also usually used in combination with parameter $messageValue.
     * @param int $messageValue Optional: only needed if you use the request parameter "action" which has the value "update". Used to show which form input was incorrect after submit or if the action was successful after submit. A value of 0 is usually interpreted as "ignore message value" / neutral.
     * @return void
     */
    public function show(array $companyData, array $appearanceData, bool $showEditButton, string $actionValue = "", int $messageValue = 0) : void;
}