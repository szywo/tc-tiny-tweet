<?php
/**
 * @package szywo\TinyTweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\TinyTweet;

use szywo\TinyTweet\Request;

/**
 * Class managing session
 *
 * Just basic functionality as security is not topic of this project.
 * However it is here almost ready to implement PHP's Manual Sessions
 * and Security hints {@link http://php.net/manual/en/session.security.php}
 *
 * @package szywo\TinyTweet
 */
class Session
{
    /**
    * Dependency: Request object
    *
    * @access private
    * @var Request
    */
    private $request;

    /**
     * Base session parameters
     *
     * @access private
     * @var array
     */
    private $sessionParameters = [
        'use_strict_mode'  => true,
        'use_cookies'      => true,
        'use_only_cookies' => true,
        'cookie_httponly'  => true,
        'cookie_lifetime'  => 0,
    ];

    /**
    * Session ID regeneration interval (seconds)
    *
    * @access private
    * @var integer
    * @see {@link http://php.net/manual/en/features.session.security.management.php}
    */
    private $sessionIdRegenerationInterval = 900;

    /**
    * Obsolete session deletion delay (seconds) as suggested by PHP manual
    *
    * @access private
    * @var integer
    * @see {@link http://php.net/manual/en/function.session-destroy.php}
    */
    private $sessionTerminationDelay = 60;

    /**
    * Session validation status
    *
    * @access private
    * @var boolean
    */
    private $valid = false;

    /**
     * Constructor
     *
     * @param Request $r Request object (DI)
     */
    public function __construct(Request $r)
    {
        $this->request = $r;
        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new Exception("Session error: Session already initiated.");
        }
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new Exception("Session error: Sessions disabled.");
        }
        session_start($this->sessionParameters);
        if (empty($_SESSION)) {
            $this->initialise();
        } else {
            $this->resume();
        }
    }

    /**
    * Initialise session's user data
    *
    * @return void
    */
    public function initUserData()
    {
        $_SESSION['data'] = array();
    }

    /**
    * Check if session's user data is set
    *
    * @param string $name  Session variable name
    * @return boolean
    */
    public function isSet(string $name)
    {
        if (array_key_exists($name, $_SESSION['data'])) {
            return true;
        }
        return false;
    }

    /**
    * Sets session's user data
    *
    * @param string $name  Session variable name
    * @param mixed  $value Session variable value
    * @return void
    */
    public function set(string $name, $value)
    {
        $_SESSION['data'][$name] = $value;
    }

    /**
    * Retrive sessions's user data
    *
    * @param string $name  Session variable name
    * @return string|null
    */
    public function get(string $name)
    {
        if (array_key_exists($name, $_SESSION['data'])) {
            return $_SESSION['data'][$name];
        }
        return null;
    }

    /**
    * Unset session's user data
    *
    * @param string $name  Session variable name
    * @return void
    */
    public function unSet(string $name)
    {
        if (array_key_exists($name, $_SESSION['data'])) {
            unset($_SESSION['data'][$name]);
        }
    }

    /**
    * Get asociative array of selected fields delivered by Request
    *
    * @param void
    * @return array
    * @see {@link Session::initialise()}
    * @see {@link Session::resume()}
    */
    private function securityRequestFields()
    {
        return array(
            'userAgent' => $this->request->userAgent(),
            'userIp' => $r->request->userIp(),
        );
    }

    /**
    * Initialise new session
    *
    * @param void
    * @return void
    */
    private function initialise()
    {
        foreach ($this->securityRequestFields() as $key => $value) {
            $_SESSION[$key] = $value;
        }
        $this->initUserData();
        $_SESSION['idCreatedTime'] = time();
        $_SESSION['SessionStartTime'] = time();
        $this->valid = true;
    }

    /**
    * Resume session
    *
    * @param void
    * @return void
    */
    private function resume()
    {
        if (isset($SESSION['obsolete']))
            if ($_SESSION['obsolete'] + $this->sessionTerminationDelay < time()) {
                $_SESSION = array();
                session_commit();
                $this->valid = false;
            }
        }
        if (isset($SESSION['idCreatedTime']) && $_SESSION['idCreatedTime'] + $this->sessionIdRegenerationInterval < time() ) {
            $this->regenerateId();
        }
    }



    /* ***** TBD ***** */

    /**
    * For logged in users unsets all session data and sets logout notification
    *
    * Function first checks if session variable 'logout' was set indicating
    * successfull logout and returns true if it was in any other case it
    * it returns false. Next it checks if there is currently logged in any
    * user
    * @return bool True if user was logged out
    */
    public function logout()
    {
        if ($this->isUserAuthorized()) {
            $this->clear();
            $_SESSION['logout'] = 'true';
            return true;
        }
        return false;
    }

    /**
    * For logged in users unsets all session data and sets logout notification
    *
    * Function first checks if session variable 'logout' was set indicating
    * successfull logout and returns true if it was in any other case it
    * it returns false. Next it checks if there is currently logged in any
    * user
    * @return bool True if user was logged out
    */
    public function checkLogoutStatus()
    {
    }
    if (isset($_SESSION['logout'])) {
        $this->clear();
        return true;
    }

    /**
    * Checks if authorized user is logged in
    *
    * @return bool
    */
    public function isUserAuthorized()
    {
        if (isset($_SESSION['userId'])) {
            return true;
        }
        return false;
    }











    /// things that probably belong elsewhere -> routing, authentication, validation

    // session

    // router
    final public function getUri($destination)
    {
        return ($this->basePath).constant('self::'.$destination)."/";
    }

    // router/obsolete
    final public function getPageName()
    {
        return $this->pageName;
    }

    // router/obsolete
    final public function getPageId()
    {
        return $this->pageId;
    }

    // router/obsolete
    final public function hasPageIdSet()
    {
        return ($this->pageId > 0)?true:false;
    }

    // router/obsolete
    final public function isPage(string $page)
    {
        if ($this->page === $page) {
            return true;
        }
        return false;
    }

    // session/authrntication
    final public function isUserAuthorized()
    {
        if (isset($_SESSION['userId'])) {
            // may include additional check to see if such user exists in db
            return true;
        }
        return false;
    }

    // authentication/vaidation
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

    // authentication
    final public function checkSignUp()
    {
        // TBC
        $errors[] = "<em>Email address</em> <strong>must not</strong> be empty.";
        $errors[] = "<em>Password</em> <strong>must not</strong> be empty.";
        $errors[] = "<em>Nickname</em> <strong>must not</strong> be empty.";
        return $errors;
    }

}
