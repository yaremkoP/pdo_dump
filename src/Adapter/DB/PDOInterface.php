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

}