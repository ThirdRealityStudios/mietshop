<?php

namespace src\mietshop\contracts;

interface IAppearanceManagementView
{
    /**
     * Shows the default color form (with the opportunity to call another form for a user-custom color).
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param string $element
     * @param array $validColors
     * @param string $selectedBootstrapColor
     * @return void
     */
    public function showDefaultColorForm(array $companyData, array $appearanceData, string $element, array $validColors, string $selectedBootstrapColor) : void;

    /**
     * Shows a color form to choose and submit a user-custom color.
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param string $element
     * @param string $hexCodeColor
     * @return string
     */
    public function showCustomColorForm(array $companyData, array $appearanceData, string $element, string $hexCodeColor) : void;

    /**
     * Shows all elements you can change the color of.
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param array $validColorElements
     * @return void
     */
    public function showAllColorElements(array $companyData, array $appearanceData, array $validColorElements) : void;

    /**
     * Shows all options (a summary) of the appearance management.
     *
     * @param array $companyData
     * @param array $appearanceData
     * @return void
     */
    public function showOptions(array $companyData, array $appearanceData) : void;

    /**
     * Shows for the specific color element that the color update was successful,
     * giving the opportunity to edit the color again,
     * using parameter $custom to do so.
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param string $element
     * @param string $custom
     * @return string
     */
    public function showColorUpdateSuccessful(array $companyData, array $appearanceData, string $element, string $custom) : void;

    /**
     * Shows for the specific color element that the color update failed,
     * giving the opportunity to edit the color again,
     * using parameter $custom to do so.
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param string $element
     * @param string $custom
     * @return string
     */
    public function showColorUpdateFailed(array $companyData, array $appearanceData, string $element, string $custom) : void;
}