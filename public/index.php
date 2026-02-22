<?php

use src\de\thirdrealitystudios\mietshop\framework\HTTPRequest;
use src\de\thirdrealitystudios\mietshop\framework\Router;

function autoloader(string $class) : void
{
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    // '..' in order to access the folder 'src' to start the web app.
    require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$class.'.php';
}

function run()
{
    spl_autoload_register("autoloader");

    $request = new HTTPRequest($_GET, $_POST, $_COOKIE, $_SERVER);

    foreach($request -> getHeaderKeys() as $key)
    {
        echo "$key: ";

        $value = $request -> getHeaderValue($key);

        echo "$value<br>";
    }



    $router = new Router();

    $router -> route($request);
}

run();
