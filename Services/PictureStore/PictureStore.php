<?php

namespace Services\PictureStore;

use Services\PictureStore\Domain\Picture as PictureDomain;
use Services\PictureStore\Model\Picture;

class PictureStore
{
    public $photoDir;
    public $trunkDir;

    public function __construct(
        $photoDir,
        $trunkDir
    ) {
        $this->photoDir = $photoDir;
        $this->trunkDir = $trunkDir;
    }

    public function process($filename)
    {
        $imageType = exif_imagetype($filename);

        if ($imageType !== IMAGETYPE_JPEG) {
            return true;
        }

        $pictureDomain = new PictureDomain($filename, $this->photoDir, $this->trunkDir);

        $picture = Picture::find([
            'hash' => $pictureDomain->hash
        ]);

        if (empty($picture)) {
            $pictureDomain->fill();
            $picture = new Picture(get_object_vars($pictureDomain));
            if (!$pictureDomain->saveFile()) {
                return false;
            }
            $picture->save();
        }

        return $picture;
    }
}
