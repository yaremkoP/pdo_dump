<?php

namespace App\Generator;

use App\Adapter\DB\PDOInterface;
use PDO;
use PDOStatement;

class SQLGenerator
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * SQLReader constructor.
     *
     * @param PDOInterface $pdo
     */
    public function __construct(PDOInterface $pdo)
    {
        $this->pdo = $pdo->getPDO();
    }

    /**
     * @return bool|\Generator
     */
    public function dump()
    {
        $tables = [];
        $query  = $this->pdo->query('SHOW TABLES');
        while ($row = $query->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        if (empty($tables)) {
            return false;
        }

        foreach ($tables as $table) {
            /** @var PDOStatement $query */
            $query    = $this->pdo->query('SELECT COUNT(*) FROM `' . $table . '`');
            $num_rows = $query->fetch(PDO::FETCH_NUM)[0];
            $query->closeCursor();

            // DROP TABLE IF EXISTS statement
            $out = 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n";

            // CREATE TABLE statement
            /** @var PDOStatement $query */
            $query = $this->pdo->query('SHOW CREATE TABLE `' . $table . '`');
            $row   = $query->fetch(PDO::FETCH_NUM);
            $query->closeCursor();

            $out .= $row[1] . ';' . "\n\n";
            yield $out;

            // INSERT INTO statements
            if ($num_rows) {
                $query       = $this->pdo->query('SELECT * FROM `' . $table . '`');
                $num_columns = $query->columnCount();

                yield 'INSERT INTO `' . $table . '` VALUES';

                $count = 0;
                while ($row = $query->fetch(PDO::FETCH_NUM)) {
                    $row = '(';
                    for ($i = 0; $i < $num_columns; $i++) {
                        $row[$i] = addslashes($row[$i]);
                        $row[$i] = preg_replace("/\n/us", "\\n", $row[$i]);
                        if (isset($row[$i])) {
                            $row .= '"' . $row[$i] . '"';
                        } else {
                            $row .= '""';
                        }
                        if ($i < ($num_columns - 1)) {
                            $row .= ',';
                        }
                    }

                    $count++;

                    if ($count < $num_rows) {
                        $row .= '),';
                    } else {
                        $row .= ");\n";
                    }

                    yield $row;
                }
            }
            $query->closeCursor();
        }
    }
}