<?php
/**
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo/tc-tiny-tweet
 */
namespace szywo\tinytweet;

/**
 * ActiveRecord representation of user.
 *
 * @package szywo\tinytweet
 */
class User
{
    /**
     * User unique id
     *
     * @access private
     * @var int
     */
    private $id;

    /**
     * User name (displayed)
     *
     * @access private
     * @var string
     */
    private $name;

    /**
     * User unique email address
     *
     * @access private
     * @var string
     */
    private $email;

    /**
     * User password (hashed)
     *
     * @access private
     * @var string
     */
    private $pass;

    /**
     * Last error info (PDOException::errorInfo)
     *
     * @access private
     * @var mixed[]
     */
    private $errorInfo = null;

    /**
     * Object representing row of user table
     *
     *
     */
    public function __construct()
    {
        $this->id    = -1;
        $this->name  = "";
        $this->email = "";
        $this->pass  = "";
    }

    /**
     * Get user id
     *
     * @param void
     * @return int User id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get user name
     *
     * @param void
     * @return string User name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user name
     *
     * @param string $name User name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get user email
     *
     * @param void
     * @return string User email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user email
     *
     * @param string $email User email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
    }

    /**
     * Get password hash
     *
     * @param void
     * @return string Hashed password
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Hash and set new password
     *
     * @param string $pass New password to hash and set
     * @return void
     */
    public function setPass($pass)
    {
        $this->pass = password_hash((string) $pass, PASSWORD_BCRYPT);
    }

    /**
     * Get errorInfo
     *
     * @param void
     * @return mixed[] PDOExeption::errorInfo
     */
    public function errorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * Save object in database
     *
     * @param \PDO $db PDO database connection
     * @return boolean True if saved with no error
     */
    public function save(\PDO $db)
    {
        $this->errorInfo = null;
        $result = false;
        if ($this->id === -1) {
            $sql = "INSERT INTO user(name, email, pass) VALUES (?, ?, ?)";
            try {
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $this->name,
                    $this->email,
                    $this->pass
                ]);
                $result = true;
                $this->id = $db->lastInsertId('id');
            } catch (\PDOException $e) {
                $this->errorInfo = $e->errorInfo;
            }
        } else {
            $sql = "UPDATE user SET name=?, email=?, pass=? WHERE id=?";
            try {
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $this->name,
                    $this->email,
                    $this->pass,
                    $this->id
                ]);
                $result = true;
            } catch (\PDOException $e) {
                $this->errorInfo = $e->errorInfo;
            }
        }
        return $result;
    }

    /**
     * Load user by id
     *
     * @param \PDO $db PDO database connection
     * @param int $id user id
     * @return User|null
     */
    public static function loadById(\PDO $db, $id)
    {
        $sql = "SELECT * FROM user WHERE id=?";
        try {
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([(int) $id]);
        } catch (\PDOException $e) {
            $result = false;
        }
        if ($result === true && $stmt->rowCount() === 1) {
            $row = $stmt->fetch();
            $user = new User();
            $user->id = $row['id'];
            $user->name = $row['name'];
            $user->email = $row['email'];
            $user->pass = $row['pass'];
            return $user;
        }
        return null;
    }

    /**
     * Load user by email
     *
     * @param \PDO $db PDO database connection
     * @param string $email User email
     * @return User|null
     */
    public static function loadByEmail(\PDO $db, $email)
    {
        $sql = "SELECT * FROM user WHERE email=?";
        try {
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([(string) $email]);
        } catch (\PDOException $e) {
            $result = false;
        }
        if ($result === true && $stmt->rowCount() === 1) {
            $row = $stmt->fetch();
            $user = new User();
            $user->id = $row['id'];
            $user->name = $row['name'];
            $user->email = $row['email'];
            $user->pass = $row['pass'];
            return $user;
        }
        return null;
    }

    /**
     * Load All users
     *
     * @param \PDO $db PDO database connection
     * @return User[]|null
     */
    public static function loadAll(\PDO $db)
    {
        $sql = "SELECT * FROM user";
        $users = [];
        try {
            $result = $db->query($sql);
        } catch (\PDOException $e) {
            $result = 0;
        }
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
                $user->pass = $row['pass'];
                $users[$row['id']] = $user;
            }
            return $users;
        }
        return null;
    }

    /**
     * Delete user from database
     *
     * @param \PDO $db PDO database connection
     * @return boolean
     */
    public function delete(\PDO $db)
    {
        $this->errorInfo = null;
        if ($this->id !== -1) {
            $sql = "DELETE FROM user WHERE id=?";
            try {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([(int) $this->id]);
            } catch (\PDOException $e) {
                $this->errorInfo = $e->errorInfo;
            }
            if ($result === true) {
                $this->id = -1;
                return true;
            }
            return false;
        }
        return true;
    }

    public function __toString()
    {
        return "id:".$this->id."; name:'".$this->name."'; email:'".$this->email."';";
    }
}
