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
    public function genSelfClosingElement(string $tag, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes, string $optionalHref = "", string $optionalMoreTagInformation = "") : string;

    /**
     * @param string $tag Name of the non-closing element in HTML, e.g. "span" or "p".
     * @param array $colorElementsCaseSensitive Here specify what appearance data should be regarded (see AppearanceModel and especially the function getAppearanceData() to understand the single data). E.g. if you want the user to format this element with the saved text color, then you would pass [ "Text" ] here. Or if you have a window with a frame, then you would usually pass [ "Window", "Frame" ] here.
     * @param array $appearanceData Obtained array by function getAppearanceData() in AppearanceModel.
     * @param array $styles An array of organized, pre-calculated stylesheets. E.g. [ "background-color: red;", "color: green;", "border-style: solid;" ] (Note the semicolon which is expected for every element!).
     * @param array $classes An array like the one expected in parameter $styles. But instead now the CSS classes you provide. E.g. [ "text-danger", "fs-6", "bg-green" ] (Note: NO semicolon needed).
     * @param string $content The usual tag or element content as usual. It could be plain text or HTML, depending on your application needs.
     * @param string $optionalHref Optional: pass href content or values here if they are required, e.g. you want to create a link / "a tag", then you could pass e.g. simply "https://www.w3schools.com". For an email you would pass e.g. "mailto:mymail@whatever.com".
     * @param string $optionalMoreTagInformation Optional: specify more tag information which should be placed there, e.g. the ID or name property. You would pass it like "id=\"myID\"" or "name=\"myName\""
     * @return string The prepared HTML element, including the content and the appearance you saved or you wanted (e.g. colors saved in the system).
     */
    public function genNonSelfClosingElement(string $tag, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes, string $content, string $optionalHref = "", string $optionalMoreTagInformation = "") : string;

    public function genSpanText(array $appearanceData, string $text) : string;

    public function genPText(array $appearanceData, string $text) : string;

    public function genALink(array $appearanceData, string $text, string $href) : string;

    public function genAButtonInteresting(array $appearanceData, string $text, string $href) : string;

    public function genAButtonUninteresting(array $appearanceData, string $text, string $href) : string;

    public function genLabelText(array $appearanceData, string $text, string $for) : string;

    /**
     * Loads a SVG file from the server disk and modifies its color,
     * using CSS.
     * Before being used,
     * it is mandatory that the given SVG file uses the attribute fill=currentColor.
     * Otherwise,
     * it can't be replaced!
     *
     * @param string $svgLocalPath Path beginning with slash, e.g. "/img/symbol/my_img.svg".
     * @param string $color E.g. "white" or "#ff0000".
     * @return string If SVG could be loaded successfully and modified, then it would be returned here as a string. Otherwise empty string (something went wrong).
     */
    public function genTextSVG(string $svgLocalPath, string $color) : string;

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
     * @param array $appearanceData
     * @param string|null $pictureURL Simply the URL to your picture which should be displayed in the post. Otherwise pass NULL here (then no picture generated / embedded).
     * @param string $descriptionHTML The HTML code you want to show in your feed (usually formatted text, a button etc.).
     * @return string Feed HTML
     */
    public function genFeed(array $appearanceData, ?string $pictureURL, string $descriptionHTML) : string;

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
    public function show(array $companyData, array $appearanceData, string $body) : void;
}