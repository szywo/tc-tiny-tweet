<?php
/**
 * TinyTweet app main controller.
 *
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo
 */

/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

$view = new \chadminick\Template();
$request = new \szywo\tinytweet\Request(__FILE__);
$view->basePath = $request->getBasePath();

// routing
$router = new \moagrius\RegexRouter();
// registration form path
$router->route(
    '/^register\/$/',
    function() use (&$view) {
        $view->title = "Register · Tiny Tweet";
        $view->bodyTemplate = "<h1>Please register!</h1>";
    }
);
// login form path
$router->route(
    '/^login\/$/',
    function() use (&$view) {
        $view->title = "Login · Tiny Tweet";
        $view->bodyTemplate = "<h1>Please login!</h1>";
    }
);
// catch all the rest -> redirect to login path
$router->route(
    '/^.*$/',
    function() use ($request) {
        header('Location: '.$request->getBasePath()."login/");
    }
);

$router->execute($request->getRequestUri());
// routing end

echo $view->render("view/pageTemplate.html.php");
