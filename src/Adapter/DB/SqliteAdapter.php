<?php

namespace App\Adapter\DB;

use PDO;

class SqliteAdapter extends BaseAdapter implements PDOInterface
{
    /**
     * @var int Default Port
     */
    protected int $default_port = 5432;

    /**
     * SqliteAdapter constructor.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->driver  = 'sqlite';

        parent::__construct($host, $database, $username, $password);
    }

}