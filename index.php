<?php
namespace szywo\TinyTweet;

/* first things first */
session_start();

/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

use szywo\TinyTweet\Template;
use szywo\TinyTweet\Controller;

$ctrl = Controller::getInstance();
$view = Template::getInstance();
$view->page_base_path = $ctrl->getBasePath();

$page = $ctrl->getPageName();

if ($page === Controller::SIGNIN_PAGE) {
    $view->page_title = "Sign in to TinyTweet · TinyTweet";
    // login form functionaity
    $view->page_css_file = "pageNotFound.css";
    $view->page_request_uri = $ctrl->getRequestUri();
    $view->page_error_box = $view->render("view/errorNotFound.html.php");
    $view->page_body = $view->render("view/pageBodyTemplate.html.php");
    http_response_code(404);
    echo $view->render("view/pageTemplate.html.php");
    exit();
}

if ($page === Controller::SIGNUP_PAGE) {
    // registration form functionality
    $view->page_title = "Error 404 - Oops!";
    $view->page_css_file = "pageNotFound.css";
    $view->page_request_uri = $ctrl->getRequestUri();
    $view->page_error_box = $view->render("view/errorNotFound.html.php");
    $view->page_body = $view->render("view/pageBodyTemplate.html.php");
    http_response_code(404);
    echo $view->render("view/pageTemplate.html.php");
    exit();
}

if ( ! $ctrl->isUserAuthorized() ) {
    // unauthenticated users go to /login/ page
    header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/" );
    exit();
} else {
    // authenticated user's zone
    $view->page_title = "Error 404 - Oops!";
    $view->page_css_file = "pageNotFound.css";
    $view->page_request_uri = $ctrl->getRequestUri();
    $view->page_error_box = $view->render("view/errorNotFound.html.php");
    $view->page_body = $view->render("view/pageBodyTemplate.html.php");
    http_response_code(404);
    echo $view->render("view/pageTemplate.html.php");
}
