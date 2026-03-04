<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\http\IHTTPRequest;

/**
 * You can use this interface to implement your own presenter which guides everything.
 * Make sure that the used view will stay "dumb" (as MVP pattern describes).
 * Use dependency injection in your constructor parameters and expect at least one view object and optionally a model object.
 * Optional is because,
 * a model might not be necessary for static views which don't have data evaluation or access
 * (e.g. a webpage which has a simple legal note to data privacy).
 * Oppositely,
 * a model is usually necessary to implement things like an account view
 * (e.g. a webpage which shows your amount of friends or your username).
 */
interface IPresenter
{
    /**
     * Processes a request or user input.
     *
     * Uses necessary data from a prepared HTTP request to trigger the appropriate view or model,
     * as described in MVP patterns.
     * This method may directly invoke or select the suitable view,
     * or first interact with the correct model,
     * await its response,
     * and then decide which view to render or which data to provide.
     * The chosen view will not have direct access to the model (MVP style - so only access to provided data).
     *
     * It was a language-independent design decision not to work with the PHP globals inside the function process(..),
     * e.g. $_SERVER, $_GET or $_POST.
     * Hence,
     * you could easily implement everything in a different language too.
     * You only have to adjust the creation ob a HTTPRequest object.
     *
     * @param IHTTPRequest $request
     * @return void
     */
    public function process(IHTTPRequest $request) : void;
}