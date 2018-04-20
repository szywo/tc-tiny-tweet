<?php
namespace szywo\TinyTweet;

/* first things first */
session_start();

/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

use szywo\TinyTweet\PageNotFound;
use szywo\TinyTweet\Controller;
use szywo\TinyTweet\DataContainer;

$ctrl = Controller::getController();
$data = DataContainer::getDataContainer();
$data->add('base_path', $ctrl->getBasePath());

$page=$ctrl->getPageName();

if ($page === Controller::SIGNIN_PAGE) {
    // login form functionaity
    $data->add('request_uri', $ctrl->getRequestUri());
    $view = new PageNotFound($data);
    $view->renderPage();
    exit();
}

if ($page === Controller::SIGNUP_PAGE) {
    // registration form functionality
    $data->add('request_uri', $ctrl->getRequestUri());
    $view = new PageNotFound($data);
    $view->renderPage();
    exit();
}

if ( ! $ctrl->isUserAuthorized() ) {
    // unauthenticated users go to /login/ page
    header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/" );
    exit();
} else {
    // authenticated user's zone
    $data->add('request_uri', $ctrl->getRequestUri());
    $view = new PageNotFound($data);
    $view->renderPage();
}
