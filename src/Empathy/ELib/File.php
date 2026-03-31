<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\MVC\Config as EConfig;

// overrides for testing purposes
if (defined('MVC_TEST_MODE')) {

    function is_uploaded_file(string $filename): bool
    {
        return file_exists($filename);
    }

    function move_uploaded_file(string $filename, string $destination): bool
    {
        return copy($filename, $destination);
    }
}



class File
{
    public string $error = '';

    public string $target = '';

    public string $target_dir = '';

    public string $filename = '';

    /** @var list<array{0: string, 1: int, 2: int}> */
    public array $deriv = [];

    public mixed $orig = null;

    public int $origX = 0;

    public int $origY = 0;

    public int $quality = 85;

    public string $gallery = '';

    private int $fs_depth = 0;

    // taken from http://php.net/manual/en/features.file-upload.multiple.php
    /**
     * @param array<string, mixed> $file_post
     *
     * @return list<array<string, mixed>>
     */
    public static function reArrayFiles(array &$file_post): array
    {


        $file_ary = [];
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i = 0; $i < $file_count; $i++) {

            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return array_values($file_ary);
    }



    /**
     * @param list<array{0: string, 1: int, 2: int}> $deriv
     */
    public function __construct(string $gallery, bool $upload, array $deriv, int $fs_depth = 0)
    {
        $this->fs_depth = $fs_depth;

        $this->gallery = $gallery;
        if ($this->gallery !== '') {
            //$this->target_dir = DOC_ROOT."/public_html/img/$this->gallery/";
            $this->target_dir = EConfig::get('DOC_ROOT').'/public_html/uploads/';
        } else {
            $this->target_dir = EConfig::get('DOC_ROOT').'/public_html/uploads/';
        }

        if (sizeof($deriv) < 1) {
            $this->deriv = [['l', 800, 600],
                                 ['tn', 200, 200],
                                 ['mid', 500, 500]];
        } else {
            $this->deriv = $deriv;
        }
        $this->quality = 85;
        $this->error = '';

        if ($upload) {
            $this->upload();
            if ($this->error === '') {
                $this->create();
                foreach ($this->deriv as $item) {
                    $this->makeDerived($item[0] . '_', $item[1], $item[2]);
                }
                $this->destroy($this->orig);
            }
        }
    }

    public function destroy(mixed $image = null): void
    {
        if ($image === null) {
            $image = $this->orig;
        }
        if ($image instanceof \GdImage) {
            imagedestroy($image);
        } elseif ($image instanceof \Imagick) {
            $image->destroy();
        }
    }

    public function create(): void
    {
        $img = \imagecreatefromjpeg($this->target);
        if (!$img instanceof \GdImage) {
            throw new \RuntimeException('Could not load JPEG image: '.$this->target);
        }
        $this->orig = $img;
        $this->origX = imagesx($img);
        $this->origY = imagesy($img);
    }

    public function makeDerived(string $prefix, int $max_width, int $max_height): void
    {
        if ($max_width < 300 || $max_height < 300) {
            $quality = 100;
        } else {
            $quality = $this->quality;
        }
        if ($this->origX > $max_width || $this->origY > $max_height) {
            $factorX = $max_width / $this->origX;
            $factorY = $max_height / $this->origY;
            if ($factorX < $factorY) {
                $factor = $factorX;
            } else {
                $factor = $factorY;
            }
        } else {
            $factor = 1;
        }
        $newX = $this->origX * $factor;
        $newY = $this->origY * $factor;

        $this->spawn($newX, $newY, $prefix, $quality);
    }

    /**
     * @param iterable<string> $files
     */
    public function resize(iterable $files): void
    {
        foreach ($files as $file) {
            $this->filename = $file;
            $this->target = $this->target_dir.$file;
            if ($this->filename !== '' && file_exists($this->target)) {
                $this->create();
                foreach ($this->deriv as $item) {
                    $this->makeDerived($item[0], $item[1], $item[2]);
                }
                $this->destroy($this->orig);
            }
        }
    }

    public function spawn(float|int $newX, float|int $newY, string $prefix, int $quality): void
    {
        $newX = max(1, (int) floor($newX));
        $newY = max(1, (int) floor($newY));
        if (!$this->orig instanceof \GdImage) {
            throw new \RuntimeException('No source image loaded');
        }
        $img = imagecreatetruecolor($newX, $newY);
        imagecopyresampled($img, $this->orig, 0, 0, 0, 0, $newX, $newY, $this->origX, $this->origY);
        $newTarget = $this->target_dir.$prefix.$this->filename;
        imagejpeg($img, $newTarget, $quality);
        $this->destroy($img);
    }

    /**
     * @param iterable<string> $files
     *
     * @return bool|int
     */
    public function remove(iterable $files): bool|int
    {
        $success_arr = [];
        $all_files = [];

        foreach ($files as $file) {
            if ($file !== '') {
                $file = urldecode($file);
                $matched = glob($this->target_dir.'*'.$file);
                $all_files = array_merge($all_files, is_array($matched) ? $matched : []);
            }
        }

        foreach ($all_files as $file) {
            array_push($success_arr, @unlink($file));
        }

        if (in_array(false, $success_arr, true)) {
            $success = false;
        } else {
            $success = sizeof($success_arr);
        }

        // no files found to remove
        $fileCount = 0;
        foreach ($files as $_) {
            $fileCount++;
        }
        if ($fileCount > 0 && $all_files === []) {
            $success = 1;
        }

        return $success;
    }

    // does not require GD
    public function getMimeType(): string
    {
        $imgInfo = getimagesize($_FILES['file']['tmp_name']);
        if ($imgInfo === false) {
            return 'application/octet-stream';
        }

        return $imgInfo['mime'];
    }

    /** @phpstan-impure */
    public function upload(): void
    {
        if ($_FILES['file']['name'] === '' || $_FILES['file']['error'] === 1) {
            $this->error .= 'Problem uploading file. Empty file?';
        } else {
            $name_array = explode('.', $_FILES['file']['name']);
            $size = sizeof($name_array);
            $ext = $name_array[$size - 1];

            /* check for jpeg */
            $mimeType = $this->getMimeType();

            if (!preg_match('/jpg|jpeg|JPG|JPEG/', $ext) || $mimeType !== 'image/jpeg') {
                $this->error .= 'Invalid file format.';
            } else {
                $name = '';
                if (sizeof($name_array) > 2) {
                    for ($i = 0; $i < $size - 1; $i++) {
                        $name .= $name_array[$i];
                        if ($i + 1 !== $size - 1) {
                            $name .= '.';
                        }
                    }
                } else {
                    $name = $name_array[0];
                }

                // new fs depth stuff
                if ($this->fs_depth > 0) {
                    $md_alpha_arr = str_split((string) preg_replace('/[^a-z]/', '', md5($this->filename)));

                    $depth_arr = array_slice($md_alpha_arr, $this->fs_depth);
                }

                $this->target = $this->target_dir.$name.'.'.$ext;
                // deal with duplicates
                $i = 1;
                while (file_exists($this->target)) {
                    $this->target = $this->target_dir.$name.'_'.$i++.'.'.$ext;
                }
                $this->filename = substr($this->target, strlen($this->target_dir));

                if (!move_uploaded_file($_FILES['file']['tmp_name'], $this->target)) {
                    $this->error .= 'Internal error';
                }
            }
        }
    }

    public function getFile(): string
    {
        return rawurlencode($this->filename);
    }

    public function getFileEncoded(): string
    {
        return htmlentities($this->filename);
    }

    public function getFsDepth(): int
    {
        return $this->fs_depth;
    }


    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return array{0: int, 1: int}
     */
    public function getDimensions(): array
    {
        return [$this->origX, $this->origY];
    }

}
