<?php

namespace src\mietshop\contracts;

interface IImprintView
{
    /**
     * @param array $companyData
     * @param string $actionValue Optional: only needed if you use the request parameter "action" and if it has the value "update". Also usually used in combination with parameter $messageValue.
     * @param int $messageValue Optional: only needed if you use the request parameter "action" which has the value "update". Used to show which form input was incorrect after submit or if the action was successful after submit. A value of 0 is usually interpreted as "ignore message value" / neutral.
     * @return void
     */
    public function show(array $companyData, string $actionValue = "", int $messageValue = 0) : void;
}