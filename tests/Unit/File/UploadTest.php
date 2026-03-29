<?php

declare(strict_types=1);

use Empathy\ELib\File\Upload;
use Empathy\MVC\Config;

describe('ELib File Upload', function () {
    $file = null;

    beforeEach(function () use (&$file) {
        $base = dirname(__DIR__, 3);

        $_FILES = [
            'file' => [
                'name' => 'new_one.mp3',
                'type' => 'audio/mpeg',
                'size' => 542,
                'tmp_name' => $base.'/tests/new_one.mp3',
                'error' => 0,
            ],
        ];

        Config::store('DOC_ROOT', $base.'/tests/tmp');

        $file = new Upload(false);
    });

    test('upload', function () use (&$file) {
        $file->upload();
        expect($file->getError())->toBe('');
        expect($file->remove([$file->file]))->toBe(1);
    });
});
