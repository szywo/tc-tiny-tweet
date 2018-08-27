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
$auth = new \szywo\tinytweet\Authentication(new \szywo\tinytweet\PhpSession());

$view->basePath = $request->getBasePath();

// routing
$router = new \moagrius\RegexRouter();

if ($auth->getUser() === null) {

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
        function() use (&$view, &$auth, $request) {
            if ($request->isMethodPost()) {
                $auth->login("TestUser");
                header('Location: '.$request->getBasePath());
            }
            $view->title = "Login · Tiny Tweet";
            $loginPath = $request->getBasePath()."login/";
            if ($auth->isRecentLogout()) {
                $message = "<h4>Succesfully loged out</h4>";
            }
            $view->bodyTemplate = ($message??"")."<h1>Please login!</h1><form accept-charset=\"UTF-8\" method=\"post\"><div class=\"form-group mb-1 mt-4 pt-2\"><input type=\"submit\" name=\"register\" class=\"btn btn-block btn-outline-primary btn-lg\" id=\"submit\" value=\"Sign up\"></div></form>";
        }
    );

    // catch all the rest -> redirect to login path
    $router->route(
        '/^.*$/',
        function() use ($request) {
            header('Location: '.$request->getBasePath()."login/");
        }
    );

} else {

    // logout route
    $router->route(
        '/^logout\/*$/',
        function() use (&$auth, $request) {
            $auth->logout();
            header('Location: '.$request->getBasePath()."login/");
        }
    );

    // fall back route
    $router->route(
        '/^.*$/',
        function() use (&$view, $auth, $request) {
            http_response_code(404);
            $view->title = "404 Not Found · Tiny Tweet";
            $requestPath = $request->getBasePath().$request->getRequestUri();
            $user = $auth->getUser();
            $view->bodyTemplate = "<h1>My apologies $user but requested uri: $requestPath was not found on this server.</h1>";
        }
    );

}

// routing end
$router->execute($request->getRequestUri());

echo $view->render("view/pageTemplate.html.php");
