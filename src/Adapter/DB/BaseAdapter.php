<?php

namespace App\Adapter\DB;

use PDO;

class BaseAdapter implements PDOInterface
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
     * BaseConnector constructor.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->host     = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        $this->init();
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
    public function getPDO(): PDO
    {
        return ($this->pdo)?:$this->pdo;
    }
}