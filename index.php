<?php
namespace szywo\TinyTweet;
/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

/* first things first */
session_start();


use szywo\TinyTweet\Template;
use szywo\TinyTweet\SessionManager;

$session = new SessionManager(__FILE__);
$view = new Template();
$view->basePath = $session->getBasePath();

$page = $session->getPageName();

// routing
if ($session->isUserAuthorized()) {
    // authenticated user's zone
    switch($page) {
        // already logged in or registered user goes to
        case SessionManager::SIGNIN_PAGE:
        case SessionManager::SIGNUP_PAGE:
            header("Location: ".$session->getBasePath());
            break;

        case SessionManager::SIGNOUT_PAGE:
            $session->logOut();
            header("Location: ".$session->getBasePath().SessionManager::SIGNIN_PAGE."/");
            break;

        case "":
        case SessionManager::MESSAGE_PAGE:
        case SessionManager::TWEET_PAGE:
        case SessionManager::USER_PAGE:
        case SessionManager::PROFILE_PAGE:
        default:
            $view->title = "Error 404 - Oops!";
            $view->cssFile = "pageNotFound.css";
            $view->requestUri = "/".$session->getRequestUri();
            $view->infoBoxTemplate = $view->render("view/infoErrorNotFound.html.php");
            $view->bodyTemplate = $view->render("view/pageBodyTemplate.html.php");
            http_response_code(404);
            echo $view->render("view/pageTemplate.html.php");
            break;
    }
    // authenticated user's zone end
} else {
    // unauthenticated users's zone
    switch ($page) {

        case SessionManager::SIGNIN_PAGE:
            // login page
            if ($session->isMethodPost()) {
                if ($session->userSignIn() === true) {
                    header("Location: ".$session->getBasePath() );
                    exit();
                } else {
                    $view->infoBoxTemplate = $view->render("view/infoErrorSignIn.html.php");
                }
            } else {
                if ($session->logOut()) {
                    $view->infoBoxTemplate = $view->render("view/infoSuccessLogOut.html.php");
                }
            }
            $view->title = "Sign in to TinyTweet · TinyTweet";
            $view->cssFile = "pageLogin.css";
            $view->signInUri = $session->getUri('SIGNIN_PAGE');
            $view->signUpUri = $session->getUri('SIGNUP_PAGE');
            $view->formBoxTemplate = $view->render("view/formSignIn.html.php");
            $view->bodyTemplate = $view->render("view/pageBodyLoginTemplate.html.php");
            echo $view->render("view/pageTemplate.html.php");
            break; // login page end

        case SessionManager::SIGNUP_PAGE:
            // new user registration page
            if ($session->isMethodPost()) {
                $errors = $session->checkSignUp();
                if ($errors === null) {
                    // register and log in user (set session data) and redirect to main page
                    exit();
                } else {
                    $view->errorMessages = $errors;
                    $view->setRaw('errorMessages');
                    $view->infoBoxTemplate = $view->render("view/infoErrorSignUp.html.php");
                }
            }
            $view->title = "Join TinyTweet · TinyTweet";
            $view->cssFile = "pageLogin.css";
            $view->signInUri = $session->getUri('SIGNIN_PAGE');
            $view->signUpUri = $session->getUri('SIGNUP_PAGE');
            $view->formBoxTemplate = $view->render("view/formSignUp.html.php");
            $view->bodyTemplate = $view->render("view/pageBodyLoginTemplate.html.php");
            echo $view->render("view/pageTemplate.html.php");
            break; // registration page end

        default:
        // unauthenticated users go to /login/ page
        header("Location: ".$session->getBasePath().SessionManager::SIGNIN_PAGE."/");

    } // unauthenticated user's page switch end
} // routing end
