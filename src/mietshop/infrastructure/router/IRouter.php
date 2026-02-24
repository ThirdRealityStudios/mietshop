<?php

namespace src\mietshop\infrastructure\router;

use src\mietshop\infrastructure\http\IHTTPRequest;

/**
 * A router is an addition to the MVP pattern because initially,
 * there is no view but an HTTP request.
 * So usually one Router instance processes the HTTP request and routes to the correct presenter (or webpage in the very end).
 * That's it mostly.
 */
interface IRouter
{
    /**
     * Processes an HTTP request object and calls the correct presenter object.
     *
     * @param IHTTPRequest $request
     * @return void
     */
    public function route(IHTTPRequest $request) : void;
}