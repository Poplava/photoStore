<?php

namespace Services\Queue\Domain;

class File
{
    public $realPath;
    public $type = 'file';

    public function __construct(\SplFileInfo $fileInfo)
    {
        $this->realPath = $fileInfo->getRealPath();
    }
}
