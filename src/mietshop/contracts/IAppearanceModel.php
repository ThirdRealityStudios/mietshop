<?php

namespace src\mietshop\contracts;

/**
 * https://getbootstrap.com/docs/5.3/customize/color/
 *
 *
 */
interface IAppearanceModel
{
    /**
     * Boostrap color names
     */
    public const ACCEPTED_BOOTSTRAP_COLORS = [ "dark", "secondary", "light", "white", "primary", "info", "danger", "success", "warning" ];

    /**
     * The elements (whether color elements (like website windows, website texts) or image elements (company logo))
     * which are allowed and known to the system to be changed.
     */
    public const ACCEPTED_ELEMENTS = [ "Color" => [ "Frame" => "äußere Linien von Rahmen", "Window" => "Fläche von Fenstern", "Background" => "Hintergrund jeder Seite", "Text" => "Texte", "Link" => "Links", "Interesting" => "Hauptfarbe", "Uninteresting" => "Nebenfarbe", "ActionExecute" => "Ausführen-Schaltfläche-Farbe", "ActionAbort" => "Abbruch-Schaltfläche-Farbe" ], "Image" => [ "Logo" => "Firmenlogo" ] ];

    /**
     * @param string $elementName Element to get the color code from (usually Bootstrap color name - like "primary", "danger", "info", "secondary" - or hex RGBA code, beginning with hashtag, e.g. #11223344)
     * @return string Color code as a simple Bootstrap color name or the hex RGBA color code (beginning with hashtag #). Or empty string if retrieving value failed.
     */
    public function getColor(string $elementName): string;

    /**
     * @param string $elementName Element to retrieve the maximum character limit for (which limits the maximum color code length)
     * @return int -1 if there was a technical error while retrieving it. Otherwise, the maximum character limit.
     */
    public function getColorCharacterLimit(string $elementName): int;

    /**
     * @param string $colorCode Color code for the specific element (e.g. Window, Frame, Text, Link). Valid values are usually the Bootstrap color names (e.g. primary, light, danger, secondary) or the RGBA hex codes (format #RRGGBBAA like #11223344).
     * @param string $elementName The specific element to change the color for.
     * @return bool True if the color could be set successfully. Otherwise, false (e.g. because it is no accepted Bootstrap color nor valid hex color code or due to technical issues).
     */
    public function setColor(string $colorCode, string $elementName): bool;

    /**
     * @param string $elementName Specifies the specific image to retrieve.
     * @return string Path of the image element, e.g. of ImageLogo for the company logo.
     */
    public function getImage(string $elementName): string;

    /**
     * @param string $elementName Specific image to ask the character limit for, e.g. ImageLogo (= company logo).
     * @return int -1 if something went wrong while retrieving. Otherwise the maximum character limit.
     */
    public function getImageCharacterLimit(string $elementName): int;

    /**
     * @param string $elementName The specific image to set the path for, e.g. "/img/logo/company/logo.png" for ImageLogo.
     * @param string $imagePath The relative path to the image.
     * @return bool True if set successfully. Otherwise, false.
     */
    public function setImage(string $elementName, string $imagePath): bool;

    /**
     * Returns all color elements you change the color of.
     *
     * It will look like this:
     *
     * [ "Frame", "Window", "Text", "Link", ... ]
     *
     * @return array
     */
    public function getColorElements() : array;

    /**
     * Returns an array which represents all necessary appearance information.
     * It currently looks like this:
     *
     * [
     *
     *  "Color" => [ "Frame" => [ "", "border-color: #ff0000;" ], "Window" => [ "primary-subtle", "" ], "Text" => [ "", "#000000" ], ... ]
     *
     * ]
     *
     * What information is given for the corresponding color element,
     * depends on what is currently saved (is it a preset Bootstrap color saved or a custom hex code color?).
     * For every color element,
     * the array element is always a possible Bootstrap color (if saved).
     * The second array element is always also a possible hex code color (if it is saved too).
     * Anyway,
     * at least one color should be saved in the two array elements (if nothing went technically wrong).
     *
     * @return array
     */
    public function getAppearanceData() : array;
}