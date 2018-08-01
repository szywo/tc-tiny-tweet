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
 * Possible improvements:
 *  1. Preffered hash algorithm selection
 *  2. Secure (https) chanel detection
 *  3. Per user session management (ie. how to unauthenticate all sessions
 *     opened by specific user)
 *  4. Logging access to expired sessions sessions
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
     * Session name
     *
     * @access private
     * @var string
     * @see setSessionName()
     */
    private $sessionName = 'PHPSESID';

    /**
     * Maximum length of session name
     *
     * @access private
     * @var integer
     * @see setSessionName()
     */
    private $sessionNameMaxLength = 64;

    /**
     * $_SESSION data container
     *
     * @access private
     * @var array
     */
    private $sessionData = [];

    /**
     * Index of $_SESSSION for storing Session's internal data
     *
     * @access private
     * @var string
     */
    private $idxSession = 'SESSION_DATA';

    /**
     * Index of $_SESSSION for storing Session's user data
     *
     * @access private
     * @var string
     */
    private $idxData = 'USER_DATA';

    /**
     * Index of $_SESSSION for storing Session's user authentication data
     *
     * @access private
     * @var string
     */
    private $idxAuth = 'AUTH_DATA';

    /**
     * Index of user's session start time
     *
     * @access private
     * @var string
     */
    private $idxSessionStartTime = 'START_TIME';

    /**
     * Index of id generation time
     *
     * @access private
     * @var string
     */
    private $idxSessionIdTime = 'ID_TIME';

    /**
     * Index of usrs's last access time
     *
     * @access private
     * @var string
     */
    private $idxSessionAccessTime = 'ACCESS_TIME';

    /**
     * Index of id's obsoletion time
     *
     * @access private
     * @var string
     */
    private $idxSessionObsoleteTime = 'OBSOLETE_TIME';

    /**
     * Index of new session's id
     *
     * @access private
     * @var string
     */
    private $idxSessionNewId = 'NEW_SESSION_ID';

    /**
     * Index of authentication status
     *
     * @access private
     * @var string
     */
    private $idxAuthStatus = 'STATUS';

    /**
     * Index of authentication additional data
     *
     * @access private
     * @var string
     */
    private $idxAuthData = 'DATA';

    /**
     * Session's default data structure
     *
     * @access private
     * @var array
     */
    private $defaultDataStructure = [
        $this->idxSession => [
            $this->idxSessionStartTime    => null,  // first arrival
            $this->idxSessionIdTime       => null,  // time session current id was genereted
            $this->idxSessionAccessTime   => null,  // last request time
            $this->idxSessionObsoleteTime => null,  // time session id become obsolete
            $this->idxSessionNewId        => null,  // assigned new session id
        ],
        $this->idxAuth    => [
            $this->idxAuthStatus => false, // authentication status
            $this->idxAuthData   => null,  // authentication addidtional user data
        ],
        $this->idxData    => null, // every other user data
    ];

    // /**
    //  * Session data verification status
    //  *
    //  * @access private
    //  * @var boolean
    //  */
    // private $sessionValid = false;

    /**
     * Time snapshoot to avoid some race contitions
     *
     * @access private
     * @var integer
     */
    private $now = null;

    /**
     * Session ID regeneration interval (seconds)
     *
     * @access private
     * @var integer
     * @see {@link http://php.net/manual/en/features.session.security.management.php}
     */
    private $ttlId = 900;

    /**
     * Obsolete session deletion delay (seconds) as suggested by PHP manual
     *
     * @access private
     * @var integer
     * @see {@link http://php.net/manual/en/function.session-destroy.php}
     */
    private $ttlObsoleteId = 60;

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
     * @param boolean $https Send session cookies only via HTTPS connection
     * @param array $securityTokens Tokens for the session's name generation engine
     */
    public function __construct(boolean $https = true, array $securityTokens = array())
    {
        if ($https !== false && !$this->isHttpsOn()) {
            throw new Exception("Session error: Secure cookies requested but connected via insecure channel.");
        }
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new Exception("Session error: Sessions disabled.");
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            // throw new Exception("Session error: Session already initiated.");
            // or we can be more gentle and quietly clear and kill existing
            // session (also we could optionaly log this event)
            $this->purge();
            // now we can just start our session like this one never existed
        }
        $this->secureSessionIniParameters($https);
        $this->setSessionName($securityTokens);
        $this->start();
        while (!$this->sessionSecure()) {}
        if (empty($this->getSession())) {
            // new or freshly reinitialised session
            $this->init();
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
            $name = "session.{$key}";
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
     * Start a session
     *
     * @param void
     * @return void
     */
    private function start();
    {
        if (session_start() === true) {
            return;
        }
        throw new Exception("Session error: Can not start new session.");
    }

    /**
     * Purge (kill/destroy/wipe) a session
     *
     * @param void
     * @return void
     * @see {@link https://stackoverflow.com/a/509056/9418958}
     */
    private function purge()
    {
        $name = session_name();
        $this->sessionData = [];
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie($name, '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        session_destroy();
        if (session_status() === PHP_SESSION_NONE
            && session_id() === ""
            && empty($_SESSION)
            && empty($this->sessionData)
        ) {
            return;
        }
        throw new Exception("Session error: Can not destroy session.");
    }

    /**
     * Create new ssession id
     *
     * @param void
     * @return string New session id
     * @see {@link https://stackoverflow.com/a/509056/9418958}
     */
    private function createId()
    {
        // php version isn't high enough
        $oldId = session_id();
        session_regenerate_id();
        $newId = session_id();
        session_commit();
        session_id($oldId);
        session_start();
        return $newId;

    }

    /**
    * Return best available hash algorithm
    *
    * @param void
    * @return string Hash algorithm name as defined by hash_algos()
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
     * @param array $securityTokens Array containing security tokens
     * @return void
     */
    private function setSessionName(array $securityTokens = array())
    {
        $algo = $this->getPrefferedHashAlgo();
        if (!empty($securityTokens) && in_array($algo, hash_algos()) {
            $str = '';
            foreach ($securityTokens as $token) {
                $str .= $token;
            }
            $this->sessionName = substr(hash($algo, $str), 0, $this->sessionNameMaxLength);
        }
        session_name($this->sessionName);
    }

    /**
    * Runs session security checks
    *
    * @param void
    * @return boolean True if session is empty or security verification was positive
    */
    private function sessionSecure()
    {
        if ($this->now === null) {
            $this->now = time();
        }
        if (empty($this->getSession()) {
            return true;
        }
        if (!$this->isDataStructureCorrect($this->getSession(), $this->defaultDataStructure)) {
            // incorrect data structure (manipulated session?, should log?)
            $this->purge();
            $this->start();
            return true;
        }
        $obsoleteTime = $this->getSessionObsoleteTime();
        if ($obsoleteTime === 0) {
            $idTime = $this->getSessionIdTime();
            if ($idTime + $this->ttlId < $this->now) {
                // time to regenerate id
                $this->setSessionObsoleteTime($this->now);
                $newId = $this->createId();
                $this->setSessionNewId($newId);
                $session = $this->getSession();
                session_commit();
                session_id($newId);
                session_start();
                $this->setSession($session);
                $this->setSessionNewId(null);
                $this->setSessionIdTime($this->now);
                $this->setSessionObsoleteTime(null);
            }
            return true;
        }
        if ($obsoleteTime + $this->ttlObsoleteId < $this->now) {
            // fully expired session access (optionally add logging)
            $this->purge();
            $this->start();
            return true;
        }
        // not fully expired session access
        $oldId = session_id();
        $newId = $this->getSessionNewId();
        session_commit();
        session_id($newId);
        session_start();
        return false;

    }



    // To consider separation of following methods as data structure from session control

    /**
    * Get all session data ($_SESSION)
    *
    * @param void
    * @return array Complete $_SESSSION array
    */
    private function getSession()
    {
        return $_SESSION;
    }

    /**
    * Get time when session was marked as obsolete
    *
    * @param void
    * @return integer Session obsolete time or 0 (if not obsolete)
    */
    private function getSessionObsoleteTime()
    {
        $obsoleteTime = ($this->session())[$this->idxSession][$this->idxSessionObsoleteTime];
        if ( $obsoleteTime === null || intval($obsoleteTime) <= 0) {
            return 0;
        }
        return intval($obsoleteTime);
    }

    /**
    * Get time when session id was generated
    *
    * @param void
    * @return integer Session id creation time or 0 (if not available)
    */
    private function getSessionIdTime()
    {
        $idTime = ($this->session())[$this->idxSession][$this->idxSessionIdTime];
        if ( $idTime === null || intval($idTime) <= 0) {
            return 0;
        }
        return intval($idTime);
    }

    /**
     * Checks if $data array key structure match $pattern array (multidimensional)
     *
     * @param array $data Array to check
     * @param array $pattern Template array
     * @return boolean True when every key from $pattern present in $data
     */
    private function isDataStructureCorrect(array $data, array $pattern)
    {
        if (empty($data) && !empty($pattern)) {
            return false;
        }
        foreach ($pattern as $key => $value) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
            if (is_array($value) && !$this->isDataStructureCorrect($data[$key], $value)) {
                return false;
            }
            // unset key already checked
            unset($data[$key]);
        }
        if (!empty[$data]) {
            // check if there are any keys left (unwanted, injected? data)
            return false;
        }
        return true;
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
