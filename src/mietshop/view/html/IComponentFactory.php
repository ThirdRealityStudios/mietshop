<?php

namespace src\mietshop\view\html;

interface IComponentFactory
{
    public function genResponsiveRow(string $contentHTML, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes) : string;

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
     * @param array $appearanceData
     * @param string $svgPath
     * @param string $returnHref
     * @param string $actionRepeatHref Optional, in the meaning of: you can give an empty string here to indicate, you don't need it. Otherwise, pass the corresponding value / string you want.
     * @param string $title
     * @param string $description
     * @return string
     */
    public function genActionResultMessage(array $appearanceData, string $svgPath, string $returnHref, string $actionRepeatHref, string $title, string $description = "") : string;

    public function genMultipleFileUploadForm(array $appearanceData, string $formAction, string $fileUploadButtonLabel, string $formFileUploadButtonName, string $formSubmitButtonTitle, string $returnHref, array $hiddenInput = []) : string;
}