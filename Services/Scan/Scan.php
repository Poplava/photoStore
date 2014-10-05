<?php

namespace Services\Scan;

class Scan
{
    private $fileExt = [];

    public function __construct($config)
    {
        if (isset($config['fileExt'])) {
            $this->fileExt = $config['fileExt'];
        }
    }

    /**
     * @param string $path
     * @throws \Exception
     *
     * @return Array
     */
    public function getFiles($path)
    {
        if (is_dir($path)) {
            return $this->filter($this->iterate($path));
        }

        if (is_file($path)) {
            $files = [new \SplFileInfo($path)];
            return $this->filter($files);
        }

        throw new \Exception('Dir not found');
    }

    private function iterate($dir)
    {
        $files = [];
        $iterator = new \FilesystemIterator($dir);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $files = array_merge($files, $this->iterate($item));
            } else {
                $files[] = $item;
            }
        }

        return $files;
    }

    /**
     * @param array $files
     *
     * @return array $out
     */
    private function filter(array $files)
    {
        $out = [];
        foreach ($files as $file) {
            if (in_array($file->getExtension(), $this->fileExt)) {
                $out[] = $file;
            }
        }

        return $out;
    }
}
