<?php

declare(strict_types=1);

namespace Empathy\ELib\File;

use Empathy\MVC\Config;

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


class Upload
{
    public string $error = '';

    public string $target = '';

    public string $target_dir = '';

    public string $file = '';

    public string $filename = '';

    public function __construct(bool $upload = true)
    {
        $this->file = '';
        $this->error = '';
        $this->target = '';
        $this->filename = '';
        $this->target_dir = Config::get('DOC_ROOT') . '/public_html/uploads/';
        if ($upload) {
            $this->upload();
        }
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getFileNameEncoded(): string
    {
        return htmlentities($this->filename);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function upload(): void
    {
        if (!isset($_FILES['file']['name']) || ($_FILES['file']['name'] === '')) {
            $this->error .= 'Problem uploading file. Empty file?';
        } else {
            $name_array = explode('.', $_FILES['file']['name']);
            $size = sizeof($name_array);
            $ext = $name_array[$size - 1];

            if (!preg_match('/mp3/i', $ext)) {
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

                $md5Hash = md5_file($_FILES['file']['tmp_name']);
                $md5 = strtolower(is_string($md5Hash) ? $md5Hash : '');
                $matches = [];
                $chars = preg_match_all('/[a-zA-Z]/', $md5, $matches);
                $charPath = 'audio/' . implode('/', array_slice($matches[0], 0, 5)) . '/';

                if (!is_dir($this->target_dir . $charPath)) {
                    mkdir($this->target_dir . $charPath, 0777, true);
                }

                $this->target = $this->target_dir . $charPath . $name . '.' . $ext;

                // deal with duplicates
                $i = 1;
                while (file_exists($this->target)) {
                    $this->target = $this->target_dir . $charPath . $name . '_' . $i++ . '.' . $ext;
                }

                $this->filename = substr($this->target, strlen($this->target_dir . $charPath));
                $this->file = substr($this->target, strlen($this->target_dir));

                if (!move_uploaded_file($_FILES['file']['tmp_name'], $this->target)) {
                    $this->error .= 'Internal error';
                }
            }
        }
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
                $matched = glob($this->target_dir . $file);
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
        return $success;
    }
}
