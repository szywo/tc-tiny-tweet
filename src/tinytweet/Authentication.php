<?php
/**
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\tinytweet;

/**
 *
 *
 * Just basic functionality as security is not topic of this project.
 * However it is here almost ready to implement PHP Manual's Sessions
 * and Security hints {@link http://php.net/manual/en/session.security.php}
 *
 * @package szywo\tinytweet
 */
class Authentication
{
    /**
     * Session class dependency object
     *
     * @access private
     * @var SessionInterface
     */
    private $session = null;

    /**
     * Session variable name for storing logged in user identifier
     *
     * @access private
     * @var string
     */
    private $userIndex = 'userId';

    /**
     * Session variable name for storing time of log in
     *
     * @access private
     * @var string
     */
    private $loginTimeIndex = 'loginTime';

    /**
     * Session variable name for storing logout event marker
     *
     * @access private
     * @var string
     */
    private $recentLogoutEventIndex = 'recentLogoutEvent';

    /**
     * Authorization management object.
     *
     * @param SessionInterface $session An object implementing SessionInterface
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Login user
     *
     * @param int|string $user
     * @return void
     */
    public function login($user)
    {
        $this->session->set($this->userIndex, $user);
        $this->session->set($this->loginTimeIndex, time());
    }

    /**
     * Logout user
     *
     * @param void
     * @return void
     */
    public function logout()
    {
        $this->session->set($this->userIndex, null);
        $this->session->set($this->recentLogoutEventIndex, true);
    }

    /**
     * Get logged user
     *
     * @param void
     * @return int|string|null User identifier or null if not logged in
     */
    public function getUser()
    {
        return $this->session->get($this->userIndex);
    }

    /**
     * Check recent logout status and reset it if it's on
     *
     * @param void
     * @return boolean True if user has recently logged out
     */
    public function isRecentLogout()
    {
        if ($this->session->get($this->recentLogoutEventIndex)) {
            $this->session->set($this->recentLogoutEventIndex, false);
            return true;
        }
        return false;
    }
}
