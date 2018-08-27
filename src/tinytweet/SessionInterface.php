<?php
/**
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\tinytweet;

/**
 * Interface for session data access
 *
 * @package szywo\tinytweet
 */
interface SessionInterface
{
    /**
     * Commit session data
     *
     * @param void
     * @return void
     */
    public function commit();

    /**
     * Clear session data
     *
     * @param void
     * @return void
     */
    public function clear();

    /**
     * Sets session data
     *
     * @param string $name Name of session variable
     * @param mixed $value Value to be set
     * @return void
     */
    public function set($name, $value);

    /**
     * Gets session data
     *
     * @param string $name Name of session variable
     * @return mixed Value of session data or null
     */
    public function get($name);

    /**
     * Checks existence of session variable
     *
     * @param string $name Name of session variable
     * @return boolean
     */
    public function isset($name);

    /**
     * Unsets session variable
     *
     * @param string $name Name of session variable
     */
    public function unset($name);
}
