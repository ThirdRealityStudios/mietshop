<?php

namespace src\de\thirdrealitystudios\mietshop\infrastructure;

class HTTPRequest
{
    // RFC-technically part of the HTTP request header (your GET parameters in URI / URI field).
    private array $query;

    // RFC-technically part of the HTTP request body.
    private array $post;

    // RFC-technically part of the HTTP request header (Cookie field).
    private array $cookies;

    /**
     * Special HTTP request header fields which are usually
     * not accessible via the key pattern "HTTP_*" in $_SERVER (= $server).
     * You can add more in future for better HTTP request recognition of the fields.
     */
    private array $specialFields = [
        "content-type" => "CONTENT_TYPE",
        "content-length" => "CONTENT_LENGTH"
    ];

    // Represents other remaining HTTP request header fields.
    private array $header;

    private array $server;

    /**
     * Tries to detect the most important HTTP request header fields and parses them into the current object,
     * ready-to-use.
     *
     * The function is meant to work this way:
     *
     * It is not a convention but a habit that PHP servers usually set the global $_SERVER with keys in the syntax "HTTP_<my-http-header-field>".
     * E.g. the HTTP header field "accept-encoding" would be on a PHP webserver "HTTP_ACCEPT_ENCODING".
     * Anyway,
     * there are exceptions.
     * Hence,
     * it might be possible not 100% of all HTTP request information is being detected but the most important fields.
     * Also,
     * RFC does not say you can't define your own request header fields.
     * Therefore,
     * it is even more complicated to detect all header fields safely without making the detection more complicated and maybe inefficient too (for critical applications especially - as PHP is an interpreter language).
     * But you should be fine with this function in most cases.
     *
     * @return void
     */
    private function initHeader() : void
    {
        $serverKeys = array_keys($this -> server);

        foreach($serverKeys as $serverKey)
        {
            if(str_starts_with($serverKey, "HTTP_"))
            {
                $shortenedServerKey = substr($serverKey, 5, strlen($serverKey) - 5);
                $modifiedServerKey = str_replace("_", "-", $shortenedServerKey);

                if(!is_string($modifiedServerKey) || strlen($modifiedServerKey) === 0)
                {
                    continue;
                }

                $this -> header[strtolower($modifiedServerKey)] = $this -> server[$serverKey];
            }
        }

        $specialFieldKeys = array_keys($this -> specialFields);

        foreach($specialFieldKeys as $specialFieldKey)
        {
            $specialFieldPHPName = $this -> specialFields[$specialFieldKey];

            if(isset($this -> server[$specialFieldPHPName]))
            {
                $this -> header[$specialFieldKey] = $this -> server[$specialFieldPHPName];
            }
        }

        /**
         * headers_list() will provide an array of complete header fields,
         * meaning each element is like "field-name: the-value" (HTTP style - just as sent).
         * Or it could be e.g. "X-Powered-By: PHP/8.2.12" for one element of $headerFields.
         */
        $headerFields = headers_list();

        // Save other header fields which were not directly given by $this -> server (possible).
        foreach($headerFields as $headerField)
        {
            $headerFieldArray = explode(": ", $headerField);
            $fieldName = $headerFieldArray[0];
            $fieldValue = $headerFieldArray[1];

            // Save it, don't care about over-writing.
            $this -> header[strtolower($fieldName)] = $fieldValue;
        }
    }

    public function __construct(array $get, array $post, array $cookies, array $server)
    {
        $this -> query = $get;
        $this -> post = $post;
        $this -> cookies = $cookies;
        $this -> server = $server;
        $this -> header = [];

        $this -> initHeader();
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