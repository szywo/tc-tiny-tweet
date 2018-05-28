<?php
/**
 * @package szywo\TinyTweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\TinyTweet;

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
     * Secure session INI parameters
     *
     * @access private
     * @var array
     * @see {@link http://php.net/manual/en/session.security.ini.php}
     */
    private $sessionSecureIniParameters = [
        'use_strict_mode'  => true,
        'use_cookies'      => true,
        'use_only_cookies' => true,
        'cookie_httponly'  => true,
        'cookie_secure'    => true,
        'cookie_lifetime'  => 0,
    ];

    /**
     * Default session name
     *
     * @access private
     * @var string
     * @see setSessionName()
     */
    private $defaultSessionName = 'PHPSESID';

    /**
     * $_SESSION security data index
     *
     * @access private
     * @var string
     * @see setSessionName()
     */
    private $securityIndex = 'SECURITY';

    /**
     * $_SESSION user data index
     *
     * @access private
     * @var string
     * @see setSessionName()
     */
    private $dataIndex = 'USER_DATA';

    /**
     * $_SESSION authentication data index
     *
     * @access private
     * @var string
     * @see setSessionName()
     */
    private $authIndex = 'USER_AUTH';

    /**
     * Maximum length of session name
     *
     * @access private
     * @var integer
     * @see setSessionName()
     */
    private $sessionNameMaxLength = 64;

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
     * Preffered hash algorithms in descending order of desirability
     *
     * @access private
     * @var array
     * @see {@link http://php.net/manual/en/function.hash-algos.php}
     */
    private $preferredHashAlgos = [
        // not a finest sellection, further research required
        'sha512',
        'sha1',
        'md5',
    ];

    /**
     * Session management object.
     *
     * Basicaly it substitutes session_start() but also provides some security
     * measures described in {@link http://php.net/manual/en/features.session.security.management.php Session Management Basics}
     *
     * @param array $securityTokens Tokens for the session's name generation engine
     * @param boolean $https Send session cookies only via HTTPS connection
     */
    public function __construct(array $securityTokens = array(), boolean $https = true)
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new Exception("Session error: Sessions disabled.");
        }
        if ($https !== false && !$this->isHttpsOn()) {
            throw new Exception("Session error: Secure cookies requested but connected via insecure channel.");
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            // throw new Exception("Session error: Session already initiated.");
            // or we can be more gentle and quietly clear and kill existing
            // session (also we could optionaly log this event)
            if ($this->purge() !== true) {
                throw new Exception("Session error: Can not destroy session initialized outside Session object.");
            }
            // now we can just start our session like this one never existed
        }
        $this->secureSessionIniParameters($https);
        $this->setSessionName($securityTokens);
        if (session_start() !== true) {
            throw new Exception("Session error: Can not start new session.");
        }


        // TBC
        if (empty($_SESSION)) {
            $this->init();
        } else {
            $this->resume();
        }
    }

    /**
     * Secures INI parameters for a session
     *
     * @param boolean $https Send session cookies only via HTTPS connection
     * @return void
     * @see $sessionSecureIniParameters
     */
    private function secureSessionIniParameters($https = true)
    {
        $sessionIniParameters = $this->sessionSecureIniParameters;
        if ($https === false) {
            $sessionIniParameters['cookie_secure'] = false;
        }
        foreach($sessionParameters as $key => $value) {
            $name = "session.".$key;
            if (ini_get($name) !== $value) {
                if (ini_set($name, $value) === false) {
                    throw new Exception("Session error: Can not set INI setting $name = $value");
                }
            }
        }
    }

    /**
     * Checks if request received through secure channel
     *
     * Many StackOverflow discusions suggests empty $_SERVER['HTTPS'] is
     * not 100% indication of insecure channel esp. if proxies are involved.
     * But it's out of scope of current project as it is test case, not
     * a production ready code.
     * @see {@link https://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps}
     *
     * @param void
     * @return boolean True if request received throug secure channel
     */
    private function isHttpsOn()
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * Purge (kill/destroy/wipe) a session
     *
     * @param void
     * @return boolean Returns true when session id and its '$_SESSION' data was destroyed
     * @see {@link https://stackoverflow.com/a/509056/9418958}
     */
    private function purge()
    {
        $name = session_name();
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie($name, '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        session_destroy();
        if (session_status() === PHP_SESSION_NONE && session_id() === "" && empty($_SESSION)) {
            return true;
        }
        return false;
    }

    /**
    * Return best available hash algorithm
    *
    * @param void
    * @return string Hash algorithm mane as defined by hash_algos()
    */
    private function getPrefferedHashAlgo()
    {
        $availableAlgos = hash_algos();
        if (empty($availableAlgos)) {
            return null;
        }
        foreach ($this->preferedHashAlgos as $algo) {
            if (in_array($algo, $availableAlgos, true)) {
                return $algo;
            }
        }
        return $availableAlgos[0];
    }

    /**
     * Set session (cookie) name.
     *
     * If security tokens are provided they will be used to generate session name.
     * Security token should stay constant on subsequent requests. Simple
     * examples are $_SERVER['REMOTE_ADDR'] or $_SERVER['HTTP_USER_AGENT']
     * but sometimes they may cause problems (proxies)
     * @param array Array containing security tokens
     * @return void
     */
    private function setSessionName(array $securityTokens)
    {
        $name = $this->defaultSessionName;
        $algo = $this->getPrefferedHashAlgo();
        if (!empty($securityTokens) && $algo !== null) {
            $str = '';
            foreach ($securityTokens as $token) {
                $str .= $token;
            }
            $name = substr(hash($algo, $str), 0, $this->sessionNameMaxLength);
        }
        session_name($name);
    }




    // TBC ************************************************

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
