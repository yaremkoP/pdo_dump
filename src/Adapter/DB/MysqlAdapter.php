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
     * MysqlAdapter constructor.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->driver  = 'mysql';
        $this->options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => true,
            PDO::ATTR_CURSOR             => PDO::CURSOR_FWDONLY,
        ];

        parent::__construct($host, $database, $username, $password);
    }

}
