<?php

namespace Services\PictureStore;

use Services\PictureStore\Domain\Picture as PictureDomain;
use Services\PictureStore\Model\Picture;

class PictureStore
{
    public function __construct(

    ) {

    }

    public function process($filename)
    {
        $imageType = exif_imagetype($filename);

        if ($imageType !== IMAGETYPE_JPEG) {
            return true;
        }

        $pictureDomain = new PictureDomain($filename);

        $picture = Picture::find([
            'hash' => $pictureDomain->hash
        ]);

        if (empty($picture)) {
            $pictureDomain->fill();
            $picture = new Picture(get_object_vars($pictureDomain));
            $picture->save();
        }

        return $picture;
    }
}
