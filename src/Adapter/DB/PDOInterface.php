<?php

namespace App\Adapter\DB;

use PDO;

interface PDOInterface
{
    /**
     * PDO getter
     *
     * @return PDO
     */
    public function getPDO(): PDO;

    /**
     * @param array $option
     */
    public function setOption(array $option);

    /**
     * @param string $host
     */
    public function setHost(string $host);

    /**
     * @param int $port
     */
    public function setPort(int $port);

    /**
     * @param string $username
     */
    public function setUsername(string $username);

    /**
     * @param string $password
     */
    public function setPassword(string $password);

    /**
     * @param string $database
     */
    public function setDatabase(string $database);
}