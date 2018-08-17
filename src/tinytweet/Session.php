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
     * Session management object.
     *
     * Basicaly it substitutes session_start() but also provides some security
     * measures described in {@link http://php.net/manual/en/features.session.security.management.php Session Management Basics}
     *
     * @param boolean $https Send session cookies only via HTTPS connection
     * @param array $securityTokens Tokens for the session's name generation engine
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new Exception("Session error: Sessions disabled.");
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->purge();
        }
        $this->start();
        while (!$this->sessionSecure()) {}
        if (empty($this->getSession())) {
            // new or freshly reinitialised session
            $this->init();
        }
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


}
