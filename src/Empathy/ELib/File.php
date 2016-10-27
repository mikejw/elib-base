<?php

namespace Empathy\ELib;

use Empathy\ELib\File\Image;
use Empathy\MVC\Config as EConfig;


// overrides for testing purposes
if (defined('MVC_TEST_MODE')) {

    function is_uploaded_file($filename) {
        return file_exists($filename);
    }
    function move_uploaded_file($filename, $destination) {
        return copy($filename, $destination);
    }
}



class File
{
    public $error;
    public $target;
    public $target_dir;
    public $filename;
    public $deriv;
    public $orig;
    public $origX;
    public $origY;
    public $quality;
    public $gallery;
    private $fs_depth;
    private $fs_dpeth_prefix;


    // taken from http://php.net/manual/en/features.file-upload.multiple.php
    public static function reArrayFiles(&$file_post) {


        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {

            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }



    public function __construct($gallery, $upload, $deriv, $fs_depth=0)
    {
        $this->fs_depth = $fs_depth;

        $this->gallery = $gallery;
        if ($this->gallery != '') {
            //$this->target_dir = DOC_ROOT."/public_html/img/$this->gallery/";
            $this->target_dir = EConfig::get('DOC_ROOT')."/public_html/uploads/";
        } else {
            $this->target_dir = EConfig::get('DOC_ROOT')."/public_html/uploads/";
        }

        if (sizeof($deriv) < 1) {
            $this->deriv = array(array('l_', 800, 600),
                                 array('tn_', 200, 200),
                                 array('mid_', 500, 500));
        } else {
            $this->deriv = $deriv;
        }
        $this->quality = 85;
        $this->error = '';

        if ($upload) {
            $this->upload();
            if ($this->error == '') {
                $this->create();
                foreach ($this->deriv as $item) {
                    $this->makeDerived($item[0], $item[1], $item[2]);
                }
                $this->destroy($this->orig);
            }
        }
    }

    public function destroy($image=null)
    {
        if ($image === null) {
            $image = $this->orig;
        }
        imageDestroy($image);
    }

    public function create()
    {
        $this->orig = \imagecreatefromjpeg($this->target);
        $this->origX = imagesx($this->orig);
        $this->origY = imagesy($this->orig);
    }

    public function makeDerived($prefix, $max_width, $max_height)
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

    public function resize($files)
    {
        foreach ($files as $file) {
            $this->filename = $file;
            $this->target = $this->target_dir.$file;
            if ($this->filename != '' && file_exists($this->target)) {
                $this->create();
                foreach ($this->deriv as $item) {
                    $this->makeDerived($item[0], $item[1], $item[2]);
                }
                $this->destroy($this->orig);
            }
        }
    }

    public function spawn($newX, $newY, $prefix, $quality)
    {
        $img = imagecreatetruecolor($newX, $newY);
        imagecopyresampled($img, $this->orig, 0, 0, 0, 0, $newX, $newY, $this->origX, $this->origY);
        $newTarget = $this->target_dir.$prefix.$this->filename;
        imagejpeg($img, $newTarget, $quality);
        $this->destroy($img);
    }

    public function remove($files)
    {
        $success_arr = array();
        $all_files = array();

        foreach ($files as $file) {
            if ($file != '') {
                $file = urldecode($file);
                $all_files = array_merge($all_files, glob($this->target_dir.'*'.$file));                
            }
        }

        foreach ($all_files as $file) {
            array_push($success_arr, @unlink($file));
        }

        if (in_array(false, $success_arr)) {
            $success = false;
        } else {
            $success = sizeof($success_arr);
        }

        return $success;
    }

    // does not require GD
    public function getMimeType()
    {
        $imgInfo = getImageSize($_FILES['file']['tmp_name']);

        return $imgInfo['mime'];
    }

    public function upload()
    {
        if ($_FILES['file']['name'] == '') {
            $this->error .= "Problem uploading file. Empty file?";
        } else {
            $name_array = explode('.', $_FILES['file']['name']);
            $size = sizeof($name_array);
            $ext = $name_array[$size-1];

            /* check for jpeg */
            $mimeType = $this->getMimeType();

            if (!preg_match('/jpg|jpeg|JPG|JPEG/', $ext) || $mimeType != 'image/jpeg') {
                $this->error .= "Invalid file format.";
            } else {
                $name = '';
                if (sizeof($name_array) > 2) {
                    for ($i = 0; $i < $size-1; $i++) {
                        $name .= $name_array[$i];
                        if ($i+1 != $size-1) {
                            $name .= '.';
                        }
                    }
                } else {
                    $name = $name_array[0];
                }

                // new fs depth stuff
                if ($this->fs_depth > 0) {
                    $md_alpha_arr = str_split(preg_replace('/[^a-z]/', '', md5($this->filename)));

                    $depth_arr = array_slice($md_alpha_arr, $this->fs_depth);
                }

                $this->target = $this->target_dir.$name.".".$ext;
                // deal with duplicates
                $i = 1;
                while (file_exists($this->target)) {
                    $this->target = $this->target_dir.$name."_".$i++.".".$ext;
                }
                $this->filename = substr($this->target, strlen($this->target_dir));

                if (!move_uploaded_file($_FILES['file']['tmp_name'], $this->target)) {
                    $this->error .= "Internal error";
                }
            }
        }
    }

    public function getFile()
    {
        return rawurlencode($this->filename);
    }

    public function getFileEncoded()
    {
        return htmlentities($this->filename);
    }

    public function getFsDepth()
    {
        return $this->fs_depth;
    }


    public function getError() {
        return $this->error;
    }


}
