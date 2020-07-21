<?php

namespace App\Writer;

class Writer implements DumpWriterInterface
{
    /**
     * @var string File path
     */
    protected string $file_path;

    /**
     * FsWriter constructor.
     *
     * @param string $file_path
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * @inheritDoc
     */
    public function write(string $out): bool
    {
        $resource = fopen($this->file_path,'a+');

        fwrite($resource,$out);

        return fclose($resource);
    }

    /**
     * @inheritDoc
     */
    public function setFilePath(string $path): bool
    {
        $this->file_path = $path;
    }
}