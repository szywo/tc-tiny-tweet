<?php
/**
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\tinytweet;

/**
 * Simple factory to instantiate database connection
 *
 * @package szywo\tinytweet
 */
class DbConnection
{
    /**
     * Read
     *
     * @param string|null $configFile Database configuration file
     * @return string[]
     */
    private static function readConfig($filename = null)
    {
        $sampleTokenFile = 'db_conf.php';
        $dbhost = null;
        $dbname = null;
        $dbuser = null;
        $dbpass = null;

        if ($filename !== null && file_exists($filename)) {
            include $filename;
        } elseif (file_exists($sampleTokenFile)) {
            include $sampleTokenFile;
        }
        return [
            'dbhost' => $dbhost,
            'dbname' => $dbname,
            'dbuser' => $dbuser,
            'dbpass' => $dbpass
        ];
    }

    /**
     * Open db connection
     *
     * @param string|null $configFile Database configuration file
     * @return \PDO
     */
    public static function open($configFile = null)
    {
        extract(self::readConfig($configFile));
        return new \PDO(
            "mysql:host=$dbhost;dbname=$dbname;charset=utf8",
            $dbuser,
            $dbpass,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
}
