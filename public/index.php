<?php

use src\mietshop\infrastructure\database\MySQLAdapter;
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
    $host = $request->getHeaderValue("host");

    if(!is_string($host))
    {
        // For safety just let the web app die because cannot set secure session cookie settings below.
        die();
    }

    // Prevent fixation attack among.
    session_set_cookie_params(
    [
        'lifetime' => 0,        // Session exists until browser gets closed
        'path' => '/',
        'domain' =>  $host, // optionally dynamcially loaded from $_SERVER['HTTP_HOST'] (here the IHTTPRequest object)
        'secure' => false,//true,       // HTTPS necessary
        'httponly' => true,     // JS access prevented
        'samesite' => 'Lax'     // CSRF protection
    ]);

    // Start a session and set a cookie called "VisitorID".
    session_name('VisitorID');
    session_start();
    
    $router = new Router();

    $databaseAdapter = new MySQLAdapter();
    $databaseAdapter->connect("localhost", "myWebsite", "root", "myPassword");

    $router -> route($request, $_SESSION, $databaseAdapter);
}

run();
