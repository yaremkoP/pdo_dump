<?php

namespace App\Adapter\DB;

use PDO;

class MysqlAdapter extends BaseAdapter implements PDOInterface
{
    /**
     * @var int Default Port
     */
    protected int $default_port = 3306;

    /**
     * MysqlConnector constructor.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->driver  = 'mysql';
        $this->port    = $this->fetchPort($host);
        $this->options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => true,
            PDO::ATTR_CURSOR             => PDO::CURSOR_FWDONLY,
        ];

        parent::__construct($host, $database, $username, $password);
    }

    /**
     * Determine port and host
     *
     * @param string $host Host string, could contain port
     *
     * @return int
     */
    private function fetchPort(string &$host)
    {
        $port = $this->default_port;
        if (strpos($host, ':')) {
            $parts = explode(':', $host);
            $host  = $parts[0];
            $port  = (int)$parts[1];
        }

        return $port;
    }
}
