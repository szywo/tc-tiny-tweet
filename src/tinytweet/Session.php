<?php
/**
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\tinytweet;

/**
 * Class managing session
 *
 * Just basic functionality as security is not topic of this project.
 * However it is here almost ready to implement PHP Manual's Sessions
 * and Security hints {@link http://php.net/manual/en/session.security.php}
 *
 * @package szywo\tinytweet
 */
class Session
{
    /**
     * $_SESSION data container
     *
     * @access private
     * @var array
     */
    private $data = array();

    /**
     * Indicates $this->data was saved in $_SESSION
     *
     * @access private
     * @var boolean
     */
    private $saved = false;

    /**
     * Session management object.
     *
     * Basicaly it substitutes session_start() but it can also be extended to
     * provide some security measures described in {@link http://php.net/manual/en/features.session.security.management.php Session Management Basics}
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new \Exception("Session error: Sessions disabled.");
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->purge();
        }
        $this->secureSessionStart();
    }

    /**
     * Destructor ensures all current session data is transfered to $_SESSION
     */
    public function __destruct()
    {
        if ($this->saved === false) {
            $this->commit();
        }
    }

    /**
     * Start
     *
     * @param void
     * @return void
     */
    protected function secureSessionStart()
    {
        $this->start();
        $this->getSessionData();
    }

    /**
     * Start a session
     *
     * @param void
     * @return void
     */
    protected function start()
    {
        if (session_start() === true) {
            return;
        }
        throw new \Exception("Session error: Can not start session.");
    }

    /**
     * Retrive session data and store it internally
     *
     * @param void
     * @return void
     */
    protected function getSessionData()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->data = $_SESSION;
            $_SESSION = array();
            $this->saved = false;
            return;
        }
        throw new \Exception("Session error: There is no active session.");
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
        $this->data = array();
        $_SESSION = array();
        $name = session_name();
        $params = session_get_cookie_params();
        setcookie(
            $name,
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        session_destroy();
        if (session_status() === PHP_SESSION_NONE
            && session_id() === ""
            && empty($_SESSION)
            && empty($this->data)
        ) {
            return;
        }
        throw new \Exception("Session error: Can not destroy session.");
    }

    /**
     * Commit session data
     *
     * @param void
     * @return void
     */
    public function commit()
    {
        $_SESSION = array();
        $_SESSION = $this->data;
        session_write_close();
        if (session_status() === PHP_SESSION_NONE) {
            $this->saved = true;
            $_SESSION = array();
            return;
        }
        throw new \Exception("Session error: Can not commit session.");
    }

    /**
     * Clear session data
     *
     * @param void
     * @return void
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * Magic setter for session data
     *
     * Magic functions allow for setting and getting session data.
     * There are discussions about best practices of using or not magic
     * __set and __get functions but in this case I agree with arguments
     * provided in this reply: {@link https://stackoverflow.com/a/6185525}
     *
     * @param string $name Name of variable (property)
     * @param mixed $value Value to be set
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Magic getter for session data
     *
     * Magic functions allow for setting and getting session data.
     * There are discussions about best practices of using or not magic
     * __set and __get functions but in this case I agree with arguments
     * provided in this reply: {@link https://stackoverflow.com/a/6185525}
     *
     * @param string $name Name of variable (property)
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null; // might throw some errors but for my purposes null is enough
    }

    /**
     * Magic function for checking existence of variable (property)
     *
     * @param string $name Name of variable (property)
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Magic function for unsetting variable (property)
     *
     * @param string $name Name of variable (property)
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
