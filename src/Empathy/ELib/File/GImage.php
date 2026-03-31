<?php

declare(strict_types=1);

namespace Empathy\ELib\File;

use Empathy\ELib\File;

/**
 * This class now uses Imagic because it
 * has wider support
 */
class GImage extends File
{
    private ?\Imagick $lastImage = null;

    public function create(): void
    {
        $this->orig = new \Imagick($this->target);
        $this->origX = $this->orig->getimagewidth();
        $this->origY = $this->orig->getimageheight();
    }

    public function spawn(float|int $newX, float|int $newY, string $prefix, int $quality): void
    {
        $newX = (int)floor($newX);
        $newY = (int) floor($newY);
        if (!$this->orig instanceof \Imagick) {
            throw new \RuntimeException('Expected Imagick source');
        }
        $this->lastImage = clone $this->orig;
        $newTarget = $this->target_dir.$prefix.$this->filename;
        $this->lastImage->resizeimage($newX, $newY, \Imagick::FILTER_UNDEFINED, 1);
        $this->lastImage->writeImage($newTarget);
        $this->destroy($this->lastImage);
    }

    public function destroy(mixed $image = null): void
    {
        if ($image === null) {
            $this->orig->destroy();
        } else {
            $image->destroy();
        }
    }

}
