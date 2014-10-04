<?php

namespace Services\Scan;

class Scan
{
    public function __construct()
    {

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
            return $this->iterate($path);
        }

        if (is_file($path)) {
            return [realpath($path)];
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
}
