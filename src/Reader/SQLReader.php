<?php

namespace App\Reader;

use App\Adapter\DB\PDOInterface;
use App\Writer\DumpWriterInterface;
use PDO;
use PDOStatement;

class SQLReader
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var DumpWriterInterface
     */
    protected DumpWriterInterface $writer;

    /**
     * SQLReader constructor.
     *
     * @param PDOInterface $pdo
     * @param DumpWriterInterface $writer
     */
    public function __construct(PDOInterface $pdo, DumpWriterInterface $writer)
    {
        $this->pdo    = $pdo->getPDO();
        $this->writer = $writer;
    }

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

        /** @var string $out Holder for dump output */
        $out = '';

        foreach ($tables as $table) {
            /** @var PDOStatement $query */
            $query    = $this->pdo->query('SELECT COUNT(*) FROM `' . $table . '`');
            $num_rows = $query->fetch(PDO::FETCH_NUM)[0];
            $query->closeCursor();

            // DROP TABLE IF EXISTS statement
            $out .= 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n";

            // CREATE TABLE statement
            /** @var PDOStatement $query */
            $query = $this->pdo->query('SHOW CREATE TABLE `' . $table . '`');
            $row   = $query->fetch(PDO::FETCH_NUM);
            $query->closeCursor();

            $out .= $row[1] . ';' . "\n\n";

            // write buffered string into file
            $this->writer->write($out);
            $out = '';

            // INSERT INTO statements
            if ($num_rows) {
                $query       = $this->pdo->query('SELECT * FROM `' . $table . '`');
                $num_columns = $query->columnCount();

                $out .= 'INSERT INTO `' . $table . '` VALUES';

                $this->writer->write($out);
                $out = '';

                $count = 0;
                while ($row = $query->fetch(PDO::FETCH_NUM)) {
                    $out = '(';
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

                    // write buffered string into file
                    $this->writer->write($out);
                    $out = '';
                }
            }
            $out .= "\n\n\n";
            $query->closeCursor();
        }

        // write buffered string into file
        $this->writer->write($out);
    }
}