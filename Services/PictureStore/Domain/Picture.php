<?php

namespace Services\PictureStore\Domain;

class Picture
{
    const STORE_TYPE_TRUNK = 'trunk';
    const STORE_TYPE_PHOTO = 'photo';

    private $exif;
    private $photoDir;
    private $trunkDir;
    private $dir;

    public $hash = null;
    public $dateTimeOriginal = null;
    public $fileDateTime = null;
    public $width = null;
    public $height = null;
    public $mimeType = '';
    public $fileSize = null;
    public $model = '';
    public $flash = null;
    public $sourceFile = '';
    public $file = '';
    public $storeType = '';
    public $processedAt = null;

    public function __construct($filename, $photoDir, $trunkDir)
    {
        $this->photoDir = $photoDir;
        $this->trunkDir = $trunkDir;
        $this->sourceFile = $filename;
        $this->exif = exif_read_data($filename);

        $this->hash = hash_file('md5', $filename);
    }

    public function fill()
    {
        if (isset($this->exif['DateTimeOriginal'])) {
            $this->dateTimeOriginal = date(DATE_ATOM, strtotime($this->exif['DateTimeOriginal']));
        }
        if (isset($this->exif['FileDateTime'])) {
            $this->fileDateTime = date(DATE_ATOM, $this->exif['FileDateTime']);
        }
        if (isset($this->exif['Model'])) {
            $this->model = $this->exif['Model'];
        }
        if (isset($this->exif['Flash'])) {
            $this->flash = $this->exif['Flash'];
        }
        if (isset($this->exif['COMPUTED'])) {
            if (isset($this->exif['COMPUTED']['Width'])) {
                $this->width = $this->exif['COMPUTED']['Width'];
            }
            if (isset($this->exif['COMPUTED']['Height'])) {
                $this->height = $this->exif['COMPUTED']['Height'];
            }
        }
        if (isset($this->exif['MimeType'])) {
            $this->mimeType = $this->exif['MimeType'];
        }
        if (isset($this->exif['FileSize'])) {
            $this->fileSize = $this->exif['FileSize'];
        }
        $this->processedAt = date(DATE_ATOM, time());

        $this->setStoreType();
        $this->setFile();
        $this->saveFile();
    }

    private function setStoreType()
    {
        if ($this->dateTimeOriginal) {
            $this->storeType = self::STORE_TYPE_PHOTO;
        } else {
            $this->storeType = self::STORE_TYPE_TRUNK;
        }
    }

    private function setFile()
    {
        if ($this->dateTimeOriginal) {
            $time = strtotime($this->dateTimeOriginal);
            $count = count(\Services\PictureStore\Model\Picture::all([
                'dateTimeOriginal' => $this->dateTimeOriginal,
                'storeType' => $this->storeType,
            ]));
        } else {
            $time = strtotime($this->fileDateTime);
            $count = count(\Services\PictureStore\Model\Picture::all([
                'fileDateTime' => $this->fileDateTime,
                'storeType' => $this->storeType,
            ]));
        }

        $ext = 'jpg';

        switch ($this->mimeType) {
            case 'image/png':
                $ext = 'png';
                break;
        }

        $basename = 'IMG_' . date('Ymd', $time) . '_' . date('His', $time);
        $dir = DIRECTORY_SEPARATOR . date('Y', $time) .
            DIRECTORY_SEPARATOR . date('m', $time) . DIRECTORY_SEPARATOR .
            date('d', $time);

        if ($this->storeType === self::STORE_TYPE_PHOTO) {
            $this->dir = $this->photoDir . $dir;
        } else {
            $this->dir = $this->trunkDir . $dir;
        }

        if ($count > 0) {
            $filename = $this->dir . DIRECTORY_SEPARATOR . $basename . '_' . $count . '.' . $ext;
        } else {
            $filename = $this->dir . DIRECTORY_SEPARATOR . $basename . '.' . $ext;
        }

        $this->file = $filename;
    }

    public function saveFile()
    {
        try {
            @mkdir($this->dir, 0755, true);
            return copy($this->sourceFile, $this->file);
        } catch (\Exception $e) {
            return false;
        }
    }
}
