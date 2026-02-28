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
     * Generates a pre-defined Bootstrap feed / post in HTML,
     * which has an optional image you can display (150px by 150px) and an HTML description
     * (any HTML of your choice theoretically but be careful what you want to embed as HTML code).
     * IMPORTANT: The existence of the given image URL is not checked!
     * This function will return the feed HTML for use on your webpage.
     * Only make sure you place the function inside a Bootstrap container like this:
     *
     * <div class="container-fluid"><?= genFeed(..); ?></div>
     *
     * @param string|null $pictureURL Simply the URL to your picture which should be displayed in the post. Otherwise pass NULL here (then no picture generated / embedded).
     * @param string $descriptionHTML The HTML code you want to show in your feed (usually formatted text, a button etc.).
     * @return string Feed HTML
     */
    public function genFeed(?string $pictureURL, string $descriptionHTML) : string;

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
     *
     * @param string $host
     * @param string $companyName
     * @param string $companyOwner
     * @param string $street
     * @param string $zipCode
     * @param string $place
     * @param string $email
     * @param string $phoneMobile
     * @param string $phoneNormal
     * @param string $mapLocationURL
     * @param string $facebookURL
     * @param string $instagramURL
     * @param string $whatsappURL
     * @param string $kleinanzeigenURL
     * @param string $privacyPolicy
     * @param string $termsOfService
     * @param string $body
     * @return void
     */
    public function show(string $host, string $companyName, string $companyOwner, string $street, string $zipCode, string $place, string $email, string $phoneMobile, string $phoneNormal, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL, string $privacyPolicy, string $termsOfService, string $body) : void;
}