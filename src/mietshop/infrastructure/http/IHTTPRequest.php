<?php

namespace src\mietshop\infrastructure\http;

/**
 * Helps the infrastructure development to enable multiple implementations
 * of an HTTP request (e.g. someone can write more efficient code, mock-objects for testing the response of the website, etc.).
 * Also,
 * this interface is not part of the "contracts" component because it is very related to PHP
 * (MVP architecture itself should be as language-independent as possible - not everybody will handle HTTP requests in an implementation of a different language)
 */
interface IHTTPRequest
{
    /**
     * Returns the given HTTP header field,
     * meaning its value.
     * If you want to get cookies,
     * then do use getCookies() instead!
     *
     * @param string $name The header field name to retrieve the value from.
     * @return string|null Whether the field value (as a string) or NULL if the field does not exist (also returns NULL for cookie).
     */
    public function getHeaderValue(string $name) : ?string;

    /**
     * Returns all keys of an HTTP request header to access all values.
     * (except the field Cookie - use the suitable get-function for that instead).
     *
     * @return array All keys (or an empty array)
     */
    public function getHeaderKeys(): array;

    /**
     * Returns the given cookie,
     * meaning its value.
     *
     * @param string $name The name of the cookie to retrieve the value from.
     * @return string|null Whether the cookie value (as a string) or NULL if the cookie does not exist.
     */
    public function getCookieValue(string $name) : ?string;

    /**
     * Returns all keys to access all cookie values sent by HTTP request.
     *
     * @return array All keys (or an empty array)
     */
    public function getCookieKeys(): array;

    /**
     * Returns the given query value (GET value from your URI string).
     *
     * @param string $name The name of the GET parameter to retrieve the value from.
     * @return string|null Whether the query value (as a string) or NULL if the query value does not exist.
     */
    public function getQueryValue(string $name) : ?string;

    /**
     * Returns all keys to access all query data (GET values) sent in an HTTP request header.
     *
     * @return array All keys (or an empty array)
     */
    public function getQueryKeys(): array;

    /**
     * Returns the given POST value (from your HTTP request body).
     *
     * @param string $name The name of the POST parameter to retrieve the value from.
     * @return string|null Whether the POST value (as a string) or NULL if the POST value does not exist.
     */
    public function getPOSTValue(string $name) : ?string;

    /**
     * Returns all keys to access all POST data sent in an HTTP request body.
     *
     * @return array All keys (or an empty array)
     */
    public function getPOSTKeys(): array;

    /**
     * Checks if there is a POST or query parameter with the given name.
     * E.g.,
     * a post parameter with the name "target" is given.
     * Then true will be returned.
     * The same would be returned if there is a request parameter of type query with the same name.
     *
     * @param string $requestParameterName Name of the parameter
     * @return bool True if the parameter does exist as a query parameter or post parameter. Otherwise, false.
     */
    public function isRequestParameterExisting(string $requestParameterName): bool;

    /**
     * Tells you if a post value or query value matches the given value.
     * This function does not differ between post and query values.
     * E.g.,
     * if you have a request parameter called "target" and want to check if the value of it is "google",
     * then it does not care if it is a post or query parameter.
     * The function will then return true.
     * Even if there are both post and query parameters with the same name and same value,
     * then this function will also return true.
     * If there is no request parameter at all with this given name,
     * then false will be returned simply.
     * The remaining logic or cases should be self-explanatory.
     *
     * @param string $requestParameterName Request parameter name (no matter if post or query parameter)
     * @param string $checkValue The given value which should be compared to the request parameter if it equals.
     * @return bool True if the post or query parameter value indeed matches the given value.
     */
    public function isRequestParameterMatching(string $requestParameterName, string $checkValue): bool;
}