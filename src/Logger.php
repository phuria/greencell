<?php

namespace App;

class Logger
{
    /**
     * @var string
     */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function log(string $log): void
    {
        file_put_contents(__DIR__.'/../'.$this->filename, "{$log}\n", FILE_APPEND);
    }
}
