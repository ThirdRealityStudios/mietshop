<?php
/**
 * This file contains function to easily generate Bootstrap popups
 * which cover the whole webpage,
 * unless accepted or met a decision.
 */

/**
 * Generates a (little) transparent fullscreen cover,
 * using Bootstrap colors,
 * which prevents users from interacting with elements from the website.
 * Should be placed as the first element of the body tag,
 * so it can cover everything which follows.
 * 
 * @param $bootstrapFluidContainerHTML The HTML code which fits into a Bootstrap fluid container, e.g. a "<div class="row">...</div>".
 * @return string HTML which represents your blocked screen with your content appearing on it, e.g. cookie popup.
 */
function genFullscreenBlocking($bootstrapFluidContainerHTML)
{
    $html = '<div class="container-fluid border border-3 border-primary bg-primary-subtle position-fixed z-3 w-100 h-100" style="opacity: 0.94;">';

    $html .= $bootstrapFluidContainerHTML;

    $html .= '</div>';

    return $html;
}

// Internal use for genFormPopup(). Ignore.
function genFirstPopupForm($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle)
{
    return '
    <form class="form mb-3" method="POST" action="'.$firstAction.'">
        <input type="hidden" name="'.$firstHiddenInputName.'" value="'.$firstHiddenInputValue.'">
        <input type="submit" class="btn btn-primary fw-bold d-block mt-3" value="'.$firstButtonTitle.'">
    </form>';
}

// Internal use for genFormPopup(). Ignore.
function genSecondPopupForm($secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle)
{
    return '
    <form class="form mb-3" method="POST" action="'.$secondAction.'">
        <input type="hidden" name="'.$secondHiddenInputName.'" value="'.$secondHiddenInputValue.'">
        <input type="submit" class="btn btn-secondary" value="'.$secondButtonTitle.'">
    </form>';
}

// Internal use for genFormPopup(). Ignore.
function genPopupForms($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle)
{
    return genFirstPopupForm($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle).genSecondPopupForm($secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);
}

/**
 * Generates a (little) transparent fullscreen popup,
 * using pre-defined Bootstrap colors and the parameters to create a form for both buttons.
 * Should be placed as the first element of the body tag,
 * so the popup can cover everything which follows.
 * The parameter description does not have to be related to a cookie consent popup,
 * as it is only an example below.
 * Bootstrap was being used to create the popup HTML.
 * The returned HTML string represents a <div class="container-fluid ...">...</div>.
 * Never use this function to generate multiple popups at once,
 * as there can be only one visible at once which covers the whole screen after the body tag.
 * 
 * @param string $preceedingHTML Maybe a title with description, like <p>Cookie consent</p><p>Please select Yes or No.</p>
 * @param string $firstAction For e.g. "https://my-domain.de/cookie/consent/"
 * @param string $firstHiddenInputName E.g. "enableAllServices" for clicking the first button.
 * @param string $firstHiddenInputValue E.g. "1" in order to symbolize "yes, I want all services to be active".
 * @param string $firstButtonTitle E.g. "Yes, activate all services".
 * @param string $secondAction Self-explanatory (it could be like $firstAction in this example).
 * @param string $secondHiddenInputName E.g. "disableAllServices".
 * @param string $secondHiddenInputValue E.g. "1" in order to say "Do it. Disable every service".
 * @param string $secondButtonTitle E.g. "No, disable all services".
 * @return string HTML which represents your fullscreen popup, using Bootstrap fluid container.
 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
 */
function genFullscreenPopup($preceedingHTML, $firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle)
{
    $formHTML = genPopupForms($firstAction, $firstHiddenInputName, $firstHiddenInputValue, $firstButtonTitle, $secondAction, $secondHiddenInputName, $secondHiddenInputValue, $secondButtonTitle);

    $popupContentHTML = '<div class="row"><div class="col-12 mt-3">'.$preceedingHTML.$formHTML.'</div></div>';

    return genFullscreenBlocking($popupContentHTML);
}
?>