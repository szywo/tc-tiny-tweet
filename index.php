<?php
/**
 * TinyTweet app main controller.
 *
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo
 */
namespace szywo\tinytweet;

use chadminick\Template;
use moagrius\RegexRouter;

/**
 * Database configuration script name.
 *
 * Sample file is present in the same directory as main index.php script, but
 * it should be copied outside of web root dir. It may pose risk of name
 * collision with another package so there is a way to change it.
 *
 * Do not change this value. See script_conf.php file.
 *
 * @var string
 */
$dbConfFileName = 'db_conf.php';

/**
 * The number of subdirectories between web root dir and main (index.php)
 * script's dir.
 *
 * Database configuration file should be placed outside of (above) web root dir.
 * Default is that main (index.php) script file is located right in web root
 * dir (hence default '0' value). If it hapens that main script is located in
 * subdir (or deeper) of web root dir change this variable accordingly.
 *
 * Do not change this value. See script_conf.php file.
 *
 * @var int
 */
$indexFileDepthRelativeToWebRoot = 0;

/**
 * Configuration script path building prefix.
 *
 * Do not change this value. See script_conf.php file.
 *
 * @var string
 */
$dbConfFilePathPrefix = '../';

/** Configuration file */
include 'script_conf.php';

/**
 * Composer autoloader
 *
 * Convenient way to autoload our own classes.
 */
require __DIR__.'/vendor/autoload.php';

// init
$request = new Request(__FILE__);
$view = new Template();
$view->basePath = $request->getBasePath();
$auth = new Authentication(new PhpSession());
$dbConfPath = "";
do {
    $dbConfPath .= $dbConfFilePathPrefix;
} while ($indexFileDepthRelativeToWebRoot-- > 0 );
try {
    $db = DbConnection::open($dbConfPath.$dbConfFileName);
} catch (\Exception $e) {
    http_response_code(500);
    $view->requestUri = $request->getRequestUri();
    $view->title = "500 Internal Server Error · Tiny Tweet";
    $view->cssFile = 'pageError.css';
    $view->errorCode = 500;
    $view->errorMsg = 'Internal server error.';
    $view->infoBoxTemplate = $view->render('view/infoServerError.html.php');
    $view->bodyTemplate = $view->render('view/pageBodyTemplate.html.php');
    echo $view->render('view/pageTemplate.html.php');
    exit();
}

// routing
$router = new RegexRouter();
$userId = $auth->getUser();
if ($userId === null || User::loadById($db, $userId) === null) {
    $view->registerUri = "register/";
    $view->loginUri = "login/";

    // registration form path
    $router->route(
        '/^register\/$/',
        function() use ($view, $auth, $request, $db) {
            $view->validate = false;
            if ($request->isMethodPost()) {
                $view->validate = true;
                $errorCnt = 0;
                $view->errorValidName = false;
                $name = $request->get('name');
                $view->registerName = htmlentities($name, ENT_QUOTES|ENT_HTML401);
                if (preg_match('/^[\w-][\w- ]{1,28}[\w-]$/', $name) !== 1 ) {
                    $errorCnt++;
                    $view->errorValidName = true;
                    $view->errorValidNameMsg = $view->render('view/formRegisterErrorName.html.php');
                } else {
                    $view->nameValidMsg = $view->render('view/formRegisterValidateOK.html.php');
                }
                $view->errorValidEmail = false;
                $email = filter_var($request->get('email'), FILTER_VALIDATE_EMAIL);
                $view->registerEmail = htmlentities($request->get('email'), ENT_QUOTES|ENT_HTML401);
                if ($email === false) {
                    $errorCnt++;
                    $view->errorValidEmail = true;
                    $view->errorValidEmailMsg = $view->render('view/formRegisterErrorEmail.html.php');
                }
                if (User::loadByEmail($db, $request->get('email')) !== null) {
                    $errorCnt++;
                    $view->errorValidEmail = true;
                    $view->errorValidEmailMsg = $view->render('view/formRegisterErrorEmailDuplicate.html.php');
                }
                if ($view->errorValidEmail === false) {
                    $view->emailValidMsg = $view->render('view/formRegisterValidateOK.html.php');
                }
                $view->errorValidPass = false;
                $view->errorValidPass2 = false;
                $pass = $request->get('pass');
                if (preg_match('/^.{5,}$/', $pass) !== 1 ) {
                    $errorCnt++;
                    $view->errorValidPass = true;
                    $view->errorValidPass2 = true;
                    $view->errorValidPassMsg = $view->render('view/formRegisterErrorPass.html.php');
                }
                $pass2 = $request->get('pass2');
                if ($pass !== $pass2) {
                    $errorCnt++;
                    $view->errorValidPass = true;
                    $view->errorValidPass2 = true;
                    $view->errorValidPass2Msg = $view->render('view/formRegisterErrorPassConfirm.html.php');
                }
                if ($errorCnt === 0) {
                    $user = new User();
                    $user->setName($name);
                    $user->setEmail($email);
                    $user->setPass($pass);
                    if ($user->save($db) !== true) {
                        http_response_code(500);
                        $view->requestUri = $request->getRequestUri();
                        $view->title = "500 Internal Server Error · Tiny Tweet";
                        $view->cssFile = 'pageError.css';
                        $view->errorCode = 500;
                        $view->errorMsg = 'Internal server error.';
                        $view->infoBoxTemplate = $view->render('view/infoServerError.html.php');
                        $view->bodyTemplate = $view->render('view/pageBodyTemplate.html.php');
                        echo $view->render('view/pageTemplate.html.php');
                        exit();
                    }
                    $auth->login($user->getId());
                    header('Location: '.$request->getBasePath());
                }
                $view->registerErrorCount = $errorCnt;
                $view->infoBoxTemplate = $view->render('view/infoErrorRegister.html.php');
            }
            $view->title = "Register · Tiny Tweet";
            $view->formBoxTemplate = $view->render('view/formRegister.html.php');
            $view->bodyTemplate = $view->render('view/pageBodyLoginTemplate.html.php');
        }
    );

    // login form path
    $router->route(
        '/^login\/$/',
        function() use ($view, $auth, $request, $db) {
            if ($request->isMethodPost()) {
                $user = User::loadByEmail($db, $request->get('email'));
                if ($user !== null && password_verify($request->get('pass'), $user->getPass())) {
                    $auth->login($user->getId());
                    header('Location: '.$request->getBasePath());
                }
                $view->authInvalid = true;
                $view->infoBoxTemplate = $view->render('view/infoErrorLogin.html.php');
                $view->userEmail = htmlentities($request->get('email'), ENT_QUOTES|ENT_HTML401);
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
        function() use ($auth, $request) {
            $auth->logout();
            header('Location: '.$request->getBasePath()."login/");
        }
    );

    // fall back route
    $router->route(
        '/^.*$/',
        function() use ($view, $request) {
            http_response_code(404);
            $requestUri = $request->getRequestUri();
            $view->title = "404 Not Found · Tiny Tweet";
            $view->cssFile = 'pageError.css';
            $view->errorCode = 404;
            $view->errorMsg = "Requested page /$requestUri was not found on this server.";
            $view->infoBoxTemplate = $view->render('view/infoServerError.html.php');
            $view->bodyTemplate = $view->render('view/pageBodyTemplate.html.php');
        }
    );

}

// routing end
$router->execute($request->getRequestUri());

echo $view->render('view/pageTemplate.html.php');
