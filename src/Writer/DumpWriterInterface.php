<?php

namespace App\Writer;

interface DumpWriterInterface
{
    /**
     * Write declaration
     *
     * @param string $out
     *
     * @return bool
     */
    public function write(string $out):bool;

    /**
     * Set path for writing file
     *
     * @param string $path
     *
     * @return bool
     */
    public function setFilePath(string $path):bool;
}