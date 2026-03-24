<?php

namespace src\mietshop\contracts;

/**
 * Mainly generates a basic and valid HTML document,
 * including yet the header and footer.
 * In between the header and footer,
 * the IGlobalView expects the body content which you provide to the function show(...) simply.
 * Additionally,
 * also provides helper functions to generate specific content,
 * like feeds.
 */
interface IGlobalView
{
    /**
     * Shows a default view,
     * including a static header, custom body and static footer.
     * A prepared associative array is expected as a parameter,
     * which expects e.g. the following associative array:
     *
     * Of course,
     * the associative values can be an empty string
     * (which will, depending on the value, determine the exact view output).
     *
     * @param array $companyData Array as described in ICompanyModel by function getCompanyData()
     * @param array $appearanceData Data provided by the AppearanceModel to know which color to choose for what element.
     * @param string $body Your body HTML
     * @return void
     */
    public function show(array $companyData, array $appearanceData, string $body): void;
}