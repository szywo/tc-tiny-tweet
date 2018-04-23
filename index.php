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


if ( ! $ctrl->isUserAuthorized() ) {

    if ($page === Controller::SIGNIN_PAGE) {
        if ($ctrl->isMethodPost()) {
            if ($ctrl->userSignIn() === true) {
                header("Location: ".$ctrl->getBasePath() );
                exit();
            } else {
                $view->page_info_box = $view->render("view/infoErrorSignIn.html.php");
            }
        } else {
            if ($ctrl->logOut()) {
                $view->page_info_box = $view->render("view/infoSuccessLogOut.html.php");
            }
        }
        $view->page_title = "Sign in to TinyTweet · TinyTweet";
        $view->page_css_file = "pageLogin.css";
        $view->page_signin_uri = $ctrl->getUri('SIGNIN_PAGE');
        $view->page_signup_uri = $ctrl->getUri('SIGNUP_PAGE');
        $view->page_form_box = $view->render("view/formSignIn.html.php");
        $view->page_body = $view->render("view/pageBodyLoginTemplate.html.php");
        echo $view->render("view/pageTemplate.html.php");
        exit();
    }

    if ($page === Controller::SIGNUP_PAGE) {
        if ($ctrl->isMethodPost()) {
            $errors = $ctrl->checkSignUp();
            if ($errors === null) {
                // register and log in user (set session data) and redirect to main page
                exit();
            } else {
                $view->page_error_messages = $errors;
                $view->page_info_box = $view->render("view/infoErrorSignUp.html.php");
            }
        }
        $view->page_title = "Join TinyTweet · TinyTweet";
        $view->page_css_file = "pageLogin.css";
        $view->page_signin_uri = $ctrl->getUri('SIGNIN_PAGE');
        $view->page_signup_uri = $ctrl->getUri('SIGNUP_PAGE');
        $view->page_form_box = $view->render("view/formSignUp.html.php");
        $view->page_body = $view->render("view/pageBodyLoginTemplate.html.php");
        echo $view->render("view/pageTemplate.html.php");
        exit();
    }
    // unauthenticated users go to /login/ page
    header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/");
    exit();

} else {
    // authenticated user's zone

    switch($page) {
        case Controller::SIGNIN_PAGE:
        case Controller::SIGNUP_PAGE:
            header("Location: ".$ctrl->getBasePath());
            break;
        case Controller::SIGNOUT_PAGE:
            $ctrl->logOut();
            header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/");
            break;
        default:
            $view->page_title = "Error 404 - Oops!";
            $view->page_css_file = "pageNotFound.css";
            $view->page_request_uri = $ctrl->getRequestUri();
            $view->page_info_box = $view->render("view/infoErrorNotFound.html.php");
            $view->page_body = $view->render("view/pageBodyTemplate.html.php");
            http_response_code(404);
            echo $view->render("view/pageTemplate.html.php");
    }

}
