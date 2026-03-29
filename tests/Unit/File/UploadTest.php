<?php

declare(strict_types=1);

use Empathy\ELib\File\Upload;
use Empathy\MVC\Config;

describe('ELib File Upload', function () {
    beforeEach(function () {
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
    });

    test('upload', function () {
        $file = new Upload(false);
        $file->upload();
        expect($file->getError())->toBe('');
        expect($file->remove([$file->file]))->toBe(1);
    });
});
