<?php

namespace src\mietshop\view\html;

interface IHtmlFactory
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
}