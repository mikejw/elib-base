<?php

namespace Empathy\ELib\File;

use Empathy\ELib\File;

/**
 * This class now uses Imagic because it 
 * has wider support
 */
class GImage extends File
{
    private $lastImage;

    public function create()
    {
        $this->orig = new \Imagick($this->target);
        $this->origX = $this->orig->getimagewidth();
        $this->origY = $this->orig->getimageheight();
    }

    public function spawn($newX, $newY, $prefix, $quality)
    {        
        $this->lastImage = clone $this->orig;        
        $newTarget = $this->target_dir.$prefix.$this->filename;
        $this->lastImage->resizeimage($newX, $newY, null, 1);
        $this->lastImage->writeImage($newTarget);
        $this->destroy($this->lastImage);
    }

    public function destroy($image=null)
    {
        if ($image === null) {
            $this->orig->destroy();
        } else {
            $image->destroy();
        }
    }

}
