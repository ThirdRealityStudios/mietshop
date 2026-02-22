<?php

namespace de\thirdrealitystudios\mietshop\framework\http\request;

class HTTPRequest
{
    // RFC-technically part of the HTTP request header (your GET parameters in URI / URI field).
    private array $query;

    // RFC-technically part of the HTTP request body.
    private array $post;

    // RFC-technically part of the HTTP request header (Cookie field).
    private array $cookies;

    private array $possibleHeaderFields = [
        "accept",
        "accept-charset",
        "accept-encoding",
        "accept-language",
        "authorization",
        "cache-control",
        "connection",
        "content-length",
        "content-md5",
        "content-type",
        "date",
        "expect",
        "forward",
        "from",
        "host",
        "if-match",
        "if-modified-since",
        "if-none-match",
        "if-range",
        "if-unmodified-since",
        "max-forwards",
        "pragma",
        "proxy-authorization",
        "range",
        "referrer",
        "te",
        "transfer-encoding",
        "upgrade",
        "user-agent",
        "via",
        "warning"
    ];

    // Represents other remaining HTTP request header fields.
    private array $header;

    public function __construct(array $get, array $post, array $cookies)
    {
        $this -> query = $get;
        $this -> post = $post;
        $this -> cookies = $cookies;
        $this -> header = [];
    }

    /**
     * Returns the given HTTP header field,
     * meaning its value.
     * If you want to get cookies,
     * then do use getCookies() instead!
     *
     * @param string $name The header field name to retrieve the value from.
     * @return string|null Whether the field value (as a string) or NULL if the field does not exist (also returns NULL for cookie).
     */
    public function getHeaderValue(string $name) : ?string
    {
        return $this -> header[strtolower($name)] ?? null;
    }

    /**
     * Returns all keys of an HTTP request header to access all values.
     * (except the field Cookie - use the suitable get-function for that instead).
     *
     * @return array All keys (or an empty array)
     */
    public function getHeaderKeys(): array
    {
        return array_keys($this -> header);
    }

    /**
     * Sets the given HTTP header field with the given value.
     *
     * @param string $value The header field name to retrieve the value from.
     * @return bool Returns true if set successfully. False if the field name is RFC-technically wrong or if it was not implemented (yet).
     */
    public function setHeaderValue(string $name, string $value) : bool
    {
        $lowerCaseName = strtolower($name);

        if(!in_array($lowerCaseName, $this -> possibleHeaderFields))
        {
            return false;
        }
        else
        {
            $this -> header[$lowerCaseName] = $value;

            return true;
        }
    }

    /**
     * Returns the given cookie,
     * meaning its value.
     *
     * @param string $name The name of the cookie to retrieve the value from.
     * @return string|null Whether the cookie value (as a string) or NULL if the cookie does not exist.
     */
    public function getCookieValue(string $name) : ?string
    {
        return $this -> cookies[$name] ?? null;
    }

    /**
     * Returns all keys to access all cookie values sent by HTTP request.
     *
     * @return array All keys (or an empty array)
     */
    public function getCookieKeys(): array
    {
        return array_keys($this -> cookies);
    }

    /**
     * Returns the given query value (GET value from your URI string).
     *
     * @param string $name The name of the GET parameter to retrieve the value from.
     * @return string|null Whether the query value (as a string) or NULL if the query value does not exist.
     */
    public function getQueryValue(string $name) : ?string
    {
        return $this -> query[$name] ?? null;
    }

    /**
     * Returns all keys to access all query data (GET values) sent in an HTTP request header.
     *
     * @return array All keys (or an empty array)
     */
    public function getQueryKeys(): array
    {
        return array_keys($this -> query);
    }

    /**
     * Returns the given POST value (from your HTTP request body).
     *
     * @param string $name The name of the POST parameter to retrieve the value from.
     * @return string|null Whether the POST value (as a string) or NULL if the POST value does not exist.
     */
    public function getPOSTValue(string $name) : ?string
    {
        return $this -> post[$name] ?? null;
    }

    /**
     * Returns all keys to access all POST data sent in an HTTP request body.
     *
     * @return array All keys (or an empty array)
     */
    public function getPOSTKeys(): array
    {
        return array_keys($this -> post);
    }
}