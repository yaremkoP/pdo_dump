<?php

namespace App\Writer;

use App\Generator\SQLGenerator;
use Generator;

class Writer implements DumpWriterInterface
{
    /**
     * @var string File path
     */
    protected string $file_path;

    /**
     * @var false|resource
     */
    protected $resource;

    /**
     * FsWriter constructor.
     *
     * @param string $file_path
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
        $this->resource  = fopen($this->file_path, 'a+');
    }

    /**
     * @inheritDoc
     */
    public function write(Generator $generator)
    {
        foreach ($generator as $line) {
            fwrite($this->resource, $line);
        }
    }

    /**
     * @inheritDoc
     */
    public function setFilePath(string $path): bool
    {
        $this->file_path = $path;
    }

    public function __destruct()
    {
        return fclose($this->resource);
    }
}