<?php
/**
 * @package szywo\TinyTweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\TinyTweet;

/**
 * Helper class to process requests
 *
 * @package szywo\TinyTweet
 */
class Request
{
    /**
     * Main script name
     *
     * @access private
     * @var string
     */
    private $scriptName;

    /**
     * Path to a script if it does not run in server's root dir
     *
     * @access private
     * @var string
     */
    private $basePath;

    /**
     * Clean request uri
     *
     * @access private
     * @var string
     */
    private $requestUri;

    /**
     * Request method
     *
     * @access private
     * @var string
     */
    private $requestMethod;

    /**
     * Request handling object.
     *
     * Process and serves informations about received request.
     *
     * @param string $scriptName Main script name, best use __FILE__
     */
    public function __construct(string $scriptName = "index.php")
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

        $this->requestMethod = 'GET';
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        }
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getRequestUri()
    {
        return $this->requestUri;
    }

    public function isMethodPost()
    {
        if ($this->requestMethod === 'POST') {
            return true;
        }
        return false;
    }

}
