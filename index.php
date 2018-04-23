<?php
namespace szywo\TinyTweet;
/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

/* first things first */
session_start();


use szywo\TinyTweet\Template;
use szywo\TinyTweet\Controller;

$ctrl = Controller::getInstance();
$view = Template::getInstance();
$view->basePath = $ctrl->getBasePath();

$page = $ctrl->getPageName();

// routing
if ($ctrl->isUserAuthorized()) {
    // authenticated user's zone
    switch($page) {
        // already logged in or registered user goes to
        case Controller::SIGNIN_PAGE:
        case Controller::SIGNUP_PAGE:
            header("Location: ".$ctrl->getBasePath());
            break;

        case Controller::SIGNOUT_PAGE:
            $ctrl->logOut();
            header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/");
            break;

        case "":
        case Controller::MESSAGE_PAGE:
        case Controller::TWEET_PAGE:
        case Controller::USER_PAGE:
        case Controller::PROFILE_PAGE:
        default:
            $view->title = "Error 404 - Oops!";
            $view->cssFile = "pageNotFound.css";
            $view->requestUri = htmlentities("/".$ctrl->getRequestUri(), ENT_QUOTES|ENT_HTML5);
            $view->infoBox = $view->render("view/infoErrorNotFound.html.php");
            $view->body = $view->render("view/pageBodyTemplate.html.php");
            http_response_code(404);
            echo $view->render("view/pageTemplate.html.php");
            break;
    }
    // authenticated user's zone end
} else {
    // unauthenticated users's zone
    switch ($page) {

        case Controller::SIGNIN_PAGE:
            // login page
            if ($ctrl->isMethodPost()) {
                if ($ctrl->userSignIn() === true) {
                    header("Location: ".$ctrl->getBasePath() );
                    exit();
                } else {
                    $view->infoBox = $view->render("view/infoErrorSignIn.html.php");
                }
            } else {
                if ($ctrl->logOut()) {
                    $view->infoBox = $view->render("view/infoSuccessLogOut.html.php");
                }
            }
            $view->title = "Sign in to TinyTweet · TinyTweet";
            $view->cssFile = "pageLogin.css";
            $view->signInUri = $ctrl->getUri('SIGNIN_PAGE');
            $view->signUpUri = $ctrl->getUri('SIGNUP_PAGE');
            $view->formBox = $view->render("view/formSignIn.html.php");
            $view->body = $view->render("view/pageBodyLoginTemplate.html.php");
            echo $view->render("view/pageTemplate.html.php");
            break; // login page end

        case Controller::SIGNUP_PAGE:
            // new user registration page
            if ($ctrl->isMethodPost()) {
                $errors = $ctrl->checkSignUp();
                if ($errors === null) {
                    // register and log in user (set session data) and redirect to main page
                    exit();
                } else {
                    $view->errorMessages = $errors;
                    $view->infoBox = $view->render("view/infoErrorSignUp.html.php");
                }
            }
            $view->title = "Join TinyTweet · TinyTweet";
            $view->cssFile = "pageLogin.css";
            $view->signInUri = $ctrl->getUri('SIGNIN_PAGE');
            $view->signUpUri = $ctrl->getUri('SIGNUP_PAGE');
            $view->formBox = $view->render("view/formSignUp.html.php");
            $view->body = $view->render("view/pageBodyLoginTemplate.html.php");
            echo $view->render("view/pageTemplate.html.php");
            break; // registration page end

        default:
        // unauthenticated users go to /login/ page
        header("Location: ".$ctrl->getBasePath().Controller::SIGNIN_PAGE."/");

    } // unauthenticated user's page switch end
} // routing end
