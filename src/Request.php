<?php
/**
 * @package szywo\TinyTweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo
 */
namespace szywo\TinyTweet;

/**
 * Class transforming absolute URI into relative URI (to main controller location)
 *
 * This class helps to avoid using "RewriteBase" statement in .htaccess file
 * so that main app controller (by default index.php) and its related files
 * can be freely moved between root and subdirs of a server without need to
 * reconfigure .htaccess file. It also proveides tools to extract only
 * relative (to main controller) part of URI.
 *
 * @package szywo\TinyTweet
 */
class Request
{
    /**
     * Holds basename(__FILE__) of main controler - by default 'index.php'
     *
     * @access private
     * @var string
     */
    private $scriptName;

    /**
     * Holds base dir where main controler is located
     *
     * @access private
     * @var string
     */
    private $basePath;

    /**
     * Contains URI relative to {@link $basePath}
     *
     * Example (main controler located in /basepath/):
     *    URL:          http://example.com/basepath/restofuri
     *    URI:          /basepath/restofuri
     *    Relative URI: /restofuri
     *
     * @access private
     * @var string
     * @see {@link $basePath}
     */
    private $relativeRequestUri;

    /**
     * {@link $relativeRequestUri} exploded into array of separate tokens
     *
     * @access private
     * @var array
     * @see {@link $relativeRequestUri}
     */
    private $reqestTokens;

    /**
     * HTTP User Agent string
     *
     * @access private
     * @var string
     */
    private $userAgent;

    /**
     * Remote IP address
     *
     * @access private
     * @var string
     */
    private $userIp;

    /**
     * Constructor
     *
     * @param string $scriptName String containing value of __FILE__
     *                           of main controller (defaults to 'index.php')
     */
    public function __construct(string $scriptName = 'index.php')
    {
        $this->scriptName = basename($scriptName);
        // set base path for cases when script runs in subdir of a server
        // /basedir/scriptName -> /basedir/
        $pattern = "/".$this->scriptName."$/";
        $this->basePath = preg_replace($pattern, "", $_SERVER['SCRIPT_NAME']??'');

        // check what is left of URI when base path is taken out
        // /basedir/subdirs_files_and_query_string
        //     -> subdirs_files_and_query_string
        $pattern = "/^".str_replace("/", "\/", $this->basePath)."/";
        $relativeRequestUri = preg_replace($pattern, "", $_SERVER['REQUEST_URI']??'');

        // exclude script's file name from leftover URI string if it's there
        $pattern = "/^".$this->scriptName."\/?/";
        $this->relativeRequestUri = preg_replace($pattern, "", $relativeRequestUri);

        // explode resulting URI string into array of subdir names
        // subdir1/subdir2/[.../][end_string]
        //    -> array('subdir1', 'subdir2'[, ...][, 'end_string'], '')
        // memo: exploding empty string returns one (empty) element array
        $this->requestTokens = explode("/",$this->relativeRequestUri);

        $this->userAgent = $_SERVER['HTTP_USER_AGENT']??'';
        $this->userIp = $_SERVER['REMOTE_ADDR']??'';
    }

    /**
     * Function returns part of URI containing base path where main controller is located
     *
     * @return string Main controller path
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Function returns part of URI relative to main controller's base path
     *
     * @return string
     * @see {@link basePath()}
     */
    public function requestUri()
    {
        return $this->relativeRequestUri;
    }

    /**
     * Function returns {@link requestUri()} exploded into array of tokens
     *
     * Example:  string 'somedir/someotherdir' -> array('somedir', 'someotherdir')
     * @return array
     * @see {@link requestUri()}
     */
    public function requestTokens()
    {
        return $this->requestTokens;
    }

    /**
     * Function checks if REQUEST_METHOD is set to 'POST'
     *
     * @return boolean
     */
    public function isMethodPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        return false;
    }
    
    /**
     * Returns user agent string as declared in HTTP_USER_AGENT
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->userAgent;
    }

    /**
     * Returns user IP address as declared in REMOTE_ADDR
     *
     * @return string
     */
    public function userIp()
    {
        return $this->userIp;
    }
