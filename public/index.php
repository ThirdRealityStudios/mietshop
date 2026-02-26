<?php

use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\infrastructure\router\Router;

function autoloader(string $class) : void
{
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    $filepath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$class.'.php';

    // '..' in order to access the folder 'src' to start the web domain.
    require_once $filepath;
}

function run()
{
    spl_autoload_register("autoloader");

    $request = new HTTPRequest($_GET, $_POST, $_COOKIE, $_SERVER);
    
    $router = new Router();

    $router -> route($request);
}

run();
