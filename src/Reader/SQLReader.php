<?php

namespace App\Reader;

use App\Adapter\DB\PDOInterface;
use App\Adapter\File\DumpWriterInterface;
use PDO;
use PDOStatement;

class SQLReader
{
    /**
     * @var PDOInterface
     */
    protected $pdo;

    /**
     * @var DumpWriterInterface
     */
    protected $writer;

    /**
     * SQLReader constructor.
     *
     * @param PDOInterface $pdo
     * @param DumpWriterInterface $writer
     */
    public function __construct(PDOInterface $pdo, DumpWriterInterface $writer)
    {
        $this->pdo    = $pdo;
        $this->writer = $writer;
    }

    public function dump()
    {
        $tables = [];
        $query  = $this->pdo->getPDO()->query('SHOW TABLES');
        while ($row = $query->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        if (empty($tables)) {
            return false;
        }

        /** @var string $out Holder for dump output */
        $out = '';

        foreach ($tables as $table) {
            /** @var PDOStatement $query */
            $query       = $this->pdo->getPDO()->query('SELECT * FROM `' . $table . '`');
            $num_columns = $query->columnCount();
            $num_rows    = $query->rowCount();

            // DROP TABLE IF EXISTS statement
            $out .= 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n";

            // CREATE TABLE statement
            /** @var PDOStatement $query_create_st */
            $query_create_st = $this->pdo->getPDO()->query('SHOW CREATE TABLE `' . $table . '`');
            $row             = $query_create_st->fetch(PDO::FETCH_NUM);

            $out .= $row[1] . ';' . "\n\n";

            // INSERT INTO statements
            if($num_rows) {
                $out .= 'INSERT INTO `'. $table . '` VALUES';

                $this->writer->write($out);
                $out = '';

                $count = 0;
                while ($row = $query->fetch(PDO::FETCH_NUM)) {
                    $out .= '(';
                    for ($i = 0; $i < $num_columns; $i++) {
                        $row[$i] = addslashes($row[$i]);
                        $row[$i] = preg_replace("/\n/us", "\\n", $row[$i]);
                        if (isset($row[$i])) {
                            $out .= '"' . $row[$i] . '"';
                        } else {
                            $out .= '""';
                        }
                        if ($i < ($num_columns - 1)) {
                            $out .= ',';
                        }
                    }

                    $count++;

                    if ($count < $num_rows) {
                        $out .= '),';
                    } else {
                        $out .= ");\n";
                    }

                    $this->writer->write($out);
                    $out = '';
                }
            }
            $out .= "\n\n\n";
            $query->closeCursor();
        }

        $this->writer->write($out);
    }
}