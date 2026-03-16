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
     * @param int $messageValue Optional: only needed if you use the request parameter "action" with the value "update", combined with a value chosen for parameter $target. 0 is usually interpreted as neutral to only show the submit form. 1 means successfully updated. -1 means failed to update. -2 means Bootstrap color name could not be updated. -3 means picked color could not be set / updated, -4 means color hex code could not be updated or set.
     * @param array $validElements Optional: Only needed if parameter $element was set too. This is due to the determination of the exact element which should be updated with the corresponding property.
     * @param array $validColors Optional: Only needed if the parameter $action = "update", $target = "color" and $element = "myColorElementChangeable". Array should look like this: [ "primary", "secondary", "danger" ]
     * @param string $oldColorSaved Optional: only needed if the parameter $validColors is also provided to show the color update form. This parameter is used to pre-select the currently saved Bootstrap color (if so).
     * @param bool $isCustomColor Optional: only needed if $oldColorSaved is also provided. Is a Bootstrap accepted color name (then 'false') given or a custom hex RGB(A) hex code (then 'true') ?
     * @return void
     */
    public function show(array $companyData, string $action, string $target = "", string $element = "", int $messageValue = 0, array $validElements = [], array $validColors = [], string $oldColorSaved = "", bool $isCustomColor = false) : void;
}