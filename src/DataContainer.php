<?php
namespace szywo\TinyTweet;

/**
 * Simple class for moving data between model, controller and view.
 *
 * It's purpose is to avoid checking everywhere if requested data was
 * in fact provided. Instead it just returns empty string.
 *
 */
class DataContainer
{
    private static $instance;

    private $data;

    /**
     * Private constructor for singleton pattern.
     *
     * @return null
     */
    private function __construct(){
        $this->data = [];
    }

    /**
     * Static function to get instance of DataContainer.
     *
     * @return $instance of DataContainer
     */
    public static function getDataContainer(){
        if (self::$instance === null){
            self::$instance = new DataContainer();
        }
        return self::$instance;
    }

    /**
     * Function replaces or inserts new $value into container under specified $key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return null
     */
    public function add(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Search if $key exists in container and return its $value or empty string.
     *
     * @param string $key
     *
     * @return mixed $value
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return '';
    }

}
