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
    $view->registerUri = "register/";
    $view->loginUri = "login/";

    // registration form path
    $router->route(
        '/^register\/$/',
        function() use (&$view) {
            $view->title = "Register · Tiny Tweet";
            $view->formBoxTemplate = $view->render('view/formRegister.html.php');
            $view->bodyTemplate = $view->render('view/pageBodyLoginTemplate.html.php');
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
            $view->formBoxTemplate = $view->render('view/formLogin.html.php');
            if ($auth->isRecentLogout()) {
                $view->infoBoxTemplate = $view->render('view/infoSuccessLogOut.html.php');
            }
            $view->bodyTemplate = $view->render('view/pageBodyLoginTemplate.html.php');
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
        function() use (&$view, $request) {
            http_response_code(404);
            $view->requestUri = $request->getRequestUri();
            $view->title = "404 Not Found · Tiny Tweet";
            $view->cssFile = 'pageNotFound.css';
            $view->infoBoxTemplate = $view->render('view/infoErrorNotFound.html.php');
            $view->bodyTemplate = $view->render('view/pageBodyTemplate.html.php');
        }
    );

}

// routing end
$router->execute($request->getRequestUri());

echo $view->render('view/pageTemplate.html.php');
