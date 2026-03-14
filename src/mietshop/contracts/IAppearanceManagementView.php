<?php

namespace src\mietshop\contracts;

interface IAppearanceManagementView
{
    /**
     * Shows the corresponding appearance management feature(s).
     *
     * @param array $companyData The company data array as described in ICompanyData
     * @param string $action The value "update" is currently only allowed or the value "all" which allows you to see all features to change the overall appearance.
     * @param string $target Optional: only needed if you have the value "update" for the parameter $action. Can be e.g. the value "logo" or "color" to show the submit form or update response message.
     * @param string $element Optional (only needed if $target is needed too): specifies the corresponding, specific element to update of the target.
     * @param int $messageValue Optional: only needed if you use the request parameter "action" with the value "update", combined with a value chosen for parameter $target. 0 is usually interpreted as neutral to only show the submit form. 1 means successfully updated. -1 means failed to update.
     * @param array $validElements Optional: Only needed if parameter $element was set too. This is due to the determination of the exact element which should be updated with the corresponding property.
     * @return void
     */
    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0, array $validElements = []) : void;
}