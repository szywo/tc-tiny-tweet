<?php

namespace szywo\TinyTweet;

final class Controller
{
    const SCRIPT_NAME = 'index.php';

    const TWEET_PAGE   = 'tweet';
    const SIGNUP_PAGE  = 'register';
    const SIGNIN_PAGE  = 'login';
    const SIGNOUT_PAGE  = 'logout';
    const USER_PAGE    = 'user';
    const PROFILE_PAGE = 'profile';
    const MESSAGE_PAGE = 'message';

    private static $instance;

    private $allowedPages = [
        self::TWEET_PAGE   => true,    // can show individual tweets by id
        self::SIGNUP_PAGE  => false,   // no parameters
        self::SIGNIN_PAGE  => false,   // no parameters
        self::SIGNOUT_PAGE  => false,  // no parameters
        self::USER_PAGE    => true,    // can show user activity by user id
        self::PROFILE_PAGE => false,   // no parameters
        self::MESSAGE_PAGE => true,    // can show private messages by id
    ];

    private $basePath;
    private $requestUri;
    private $pageName;
    private $pageId;
    private $userId;
    private $authenticatedUser;

    // we use Singleton for configuration data hence private constructor
    final private function __construct()
    {
        // set base path for cases when script runs in subdir of a server
        $basePath = str_replace(self::SCRIPT_NAME,"",$_SERVER['SCRIPT_NAME']);;
        $this->basePath = $basePath;

        // check what is left of URI when host and base path is taken out
        $requestUri = str_replace($basePath,"",$_SERVER['REQUEST_URI']);
        $requestUri = str_replace(self::SCRIPT_NAME."/","",$requestUri);
        $this->requestUri = $requestUri;

        // exploding empty string returns one (empty) element array
        $requestedPage = explode("/",$requestUri);
        //now check if valid page was requested
        if (array_key_exists($requestedPage[0], $this->allowedPages)) {
            $this->pageName = $requestedPage[0];
            $this->pageId = -1;
            if ($this->allowedPages[$this->pageName]) {
                $options = ['options' => ['default' => -1, 'min_range' => 1]];
                $this->pageId = filter_var($requestedPage[1], FILTER_VALIDATE_INT, $options);
            }
        } else {
            $this->pageName = '';
            $this->pageId = -1;
        }
    }

    final public static function getInstance()
    {
        if (self::$instance === null){
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    final public function getBasePath()
    {
        return $this->basePath;
    }

    final public function getRequestUri()
    {
        return $this->requestUri;
    }

    final public function getPageName()
    {
        return $this->pageName;
    }

    final public function pageId()
    {
        return $this->pageId;
    }

    final public function hasPageIdSet()
    {
        return ($this->pageId > 0)?true:false;
    }

    final public function isPage(string $page)
    {
        if ($this->page === $page) {
            return true;
        }
        return false;
    }

    final public function isUserAuthorized()
    {
        if (isset($_SESSION['userId'])) {
            // may include additional check to see if such user exists in db
            return true;
        }
        return false;
    }

    final public function isMethodPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        return false;
    }

    final public function userSignIn()
    {
        if (isset($_POST['email']) && isset($_POST['pass']) ) {
            // here we should consult User class methods to check user
            // for now do it staticaly
            if ($_POST['email'] === "jan@example.com"
                && $_POST['pass'] === "go") {
                $_SESSION['userId'] = 1;
                $_SESSION['email'] = 'jan@example.com';
                $_SESSION['nick'] = 'Jan';
                return true;
            }
        }
        return null;
    }

    final public function getUri($destination)
    {
        return ($this->basePath).constant('self::'.$destination)."/";
    }

    final public function checkSignUp()
    {
        // TBC
        $errors[] = "<em>Username</em> field <strong>must not</strong> be empty.";
        $errors[] = "<em>Email address</em> field <strong>must not</strong> be empty.";
        $errors[] = "Password <strong>must not</strong> be empty.";
        return $errors;
    }

    final public function logOut() {
        if (isset($_SESSION['logOut'])) {
            $_SESSION = array();
            return true;
        }
        if (isset($_SESSION['userId'])) {
            $_SESSION = array ();
            $_SESSION['logOut'] = 'true';
        }
        return false;
    }
}
