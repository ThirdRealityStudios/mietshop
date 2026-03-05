<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\http\IHTTPRequest;

interface ILoginPresenter
{
    /**
     * Handles the login or authentification.
     *
     * There are 3 cases this presenter should handle:
     *
     * 1. show login page if not yet logged in
     *
     * 2. verify credentials after login form submitted (and start session)
     *
     * 3. redirect to the correct page if a user is yet logged in
     *
     * The expected post values and session values array is an associative array (mostly just $_POST and $_SESSION in PHP),
     * having once the key and then the value for each element,
     * e.g.:
     *
     * [
     *
     * "Email" => "mymail@mail.com",
     *
     * "Password" => "myPassword123"
     *
     * ]
     *
     * @param IHTTPRequest $httpRequest
     * @param array $sessionValues (passed-by-reference)
     * @return void
     */
    public function process(IHTTPRequest &$httpRequest, array &$sessionValues) : void;
}