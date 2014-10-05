<?php

namespace Services\PictureStore\Domain;

class Picture
{
    const STORE_TYPE_TRUNK = 'trunk';
    const STORE_TYPE_PHOTO = 'photo';

    private $exif;

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

    public function __construct($filename)
    {
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
    }

    private function setStoreType()
    {
        if ($this->dateTimeOriginal) {
            $this->storeType = self::STORE_TYPE_PHOTO;
        } else {
            $this->storeType = self::STORE_TYPE_TRUNK;
        }
    }
}
