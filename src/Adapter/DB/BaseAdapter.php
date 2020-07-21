<?php

namespace App\Adapter\DB;

use PDO;

abstract class BaseAdapter implements PDOInterface
{
    /**
     * @var string DB driver
     */
    protected string $driver;

    /**
     * @var string Username
     */
    protected string $username;

    /**
     * @var string Password
     */
    protected string $password;

    /**
     * @var string DB host
     */
    protected string $host;

    /**
     * @var int Port
     */
    protected int $port;

    /**
     * @var string Database
     */
    protected string $database;

    /**
     * @var array
     */
    protected array $option = [];

    /**
     * @var PDO PDO instance
     */
    protected PDO $pdo;

    /**
     * Set 3306 as very popular port.
     * Could be redeclared at child class.
     *
     * @var int Default Port
     */
    protected int $default_port = 3306;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * BaseConnector constructor.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct(string $host, string $database, string $username, string $password, array $options = [])
    {
        $this->host     = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port     = $this->default_port;
        $this->port     = $this->fetchPort($host);
    }

    /**
     * Init PDO object
     */
    protected function init()
    {
        $dsn = $this->driver . ':host=' . $this->host . ';port=' . (string)$this->port . ';dbname=' . $this->database . ';charset=utf8';

        $this->pdo = new PDO($dsn, $this->username, $this->password, $this->options);
    }

    /**
     * Magic getter method
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }
    }

    /**
     * @return PDO
     */
    final public function getPDO(): PDO
    {
        $this->init();

        return $this->pdo;
    }

    /**
     * @param array $option
     *
     * @return $this
     */
    final public function setOption(array $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    final public function setHost(string $host)
    {
        $this->host = $host;
        $this->port = $this->fetchPort($host);

        return $this;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    final public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $database
     *
     * @return $this
     */
    final public function setDatabase(string $database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    final public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    final public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Determine port and host
     *
     * @param string $host Host string, could contain port
     *
     * @return int
     */
    protected function fetchPort(string &$host)
    {
        $port = $this->port;
        if (strpos($host, ':')) {
            $parts = explode(':', $host);
            $host  = $parts[0];
            $port  = (int)$parts[1];
        }

        return $port;
    }
}