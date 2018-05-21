<?php
namespace szywo\TinyTweet;

final class SessionManager
{

    const TWEET_PAGE   = 'tweet';
    const SIGNUP_PAGE  = 'register';
    const SIGNIN_PAGE  = 'login';
    const SIGNOUT_PAGE = 'logout';
    const USER_PAGE    = 'user';
    const PROFILE_PAGE = 'profile';
    const MESSAGE_PAGE = 'message';

    private $pagesParameters = [
        self::TWEET_PAGE    => true,    // can show individual tweets by id
        self::SIGNUP_PAGE   => false,   // no parameters
        self::SIGNIN_PAGE   => false,   // no parameters
        self::SIGNOUT_PAGE  => false,   // no parameters
        self::USER_PAGE     => true,    // can show user activity by user id
        self::PROFILE_PAGE  => false,   // no parameters
        self::MESSAGE_PAGE  => true,    // can show private messages by id
    ];

    private $scriptName;
    private $basePath;
    private $requestUri;
    private $pageName;
    private $pageId;
    private $userId;
    private $authenticatedUser;

    final public function __construct(string $scriptName)
    {
        $this->scriptName = basename($scriptName);
        // set base path for cases when script runs in subdir of a server
        // /basedir/script.php -> basedir/
        $pattern = "/".$this->scriptName."$/";
        $basePath = preg_replace($pattern, "", $_SERVER['SCRIPT_NAME']);
        $this->basePath = $basePath;

        // check what is left of URI when base path is taken out
        // /basedir/subdirs_files_and_query_string
        //     -> subdirs_files_and_query_string
        $pattern = "/^".str_replace("/", "\/", $basePath)."/";
        $requestUri = preg_replace($pattern, "", $_SERVER['REQUEST_URI']);

        // exclude script's file name from leftover URI string if it's there
        $pattern = "/^".$this->scriptName."\/?/";
        $requestUri = preg_replace($pattern, "", $requestUri);
        $this->requestUri = $requestUri;

        // explode resulting URI string into array of subdir names
        // subdir1/subdir2/[.../][end_string]
        //    -> array('subdir1', 'subdir2'[, ...][, 'end_string'], '')
        // note: exploding empty string returns one (empty) element array
        $requestedPage = explode("/",$requestUri);

        // probably this should be router responsibility (TBD)
        //now check if valid page was requested
        if (array_key_exists($requestedPage[0], $this->pagesParameters)) {
            $this->pageName = $requestedPage[0];
            $this->pageId = -1;
            // check if requested page can receive additional id parameter
            // and extract it
            if ($this->pagesParameters[$this->pageName]) {
                $options = ['options' => ['default' => -1, 'min_range' => 1]];
                $this->pageId = filter_var($requestedPage[1], FILTER_VALIDATE_INT, $options);
            }
        } else {
            $this->pageName = '';
            $this->pageId = -1;
        }
    }

    final public function getBasePath()
    {
        return $this->basePath;
    }

    final public function getRequestUri()
    {
        return $this->requestUri;
    }

    final public function isMethodPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        return false;
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




    /// things that probably belong elsewhere -> routing, authentication, validation
    final public function getUri($destination)
    {
        return ($this->basePath).constant('self::'.$destination)."/";
    }

    final public function getPageName()
    {
        return $this->pageName;
    }

    final public function getPageId()
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

    final public function userSignIn()
    {
        if (isset($_POST['email']) && isset($_POST['pass']) ) {
            // here we should consult User class methods to check user
            // for now do it staticaly
            if ($_POST['email'] === "jan@k.pl"
                && $_POST['pass'] === "jan") {
                $_SESSION['userId'] = 1;
                $_SESSION['email'] = 'jan@k.pl';
                $_SESSION['nick'] = 'Jan';
                return true;
            }
        }
        return null;
    }

    final public function checkSignUp()
    {
        // TBC
        $errors[] = "<em>Email address</em> <strong>must not</strong> be empty.";
        $errors[] = "<em>Password</em> <strong>must not</strong> be empty.";
        $errors[] = "<em>Nickname</em> <strong>must not</strong> be empty.";
        return $errors;
    }

}
