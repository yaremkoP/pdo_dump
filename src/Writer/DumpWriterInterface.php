<?php

namespace App\Writer;

use Generator;

interface DumpWriterInterface
{
    /**
     * Write declaration
     *
     * @param Generator $generator
     */
    public function write(Generator $generator);

    /**
     * Set path for writing file
     *
     * @param string $path
     *
     * @return bool
     */
    public function setFilePath(string $path):bool;
}