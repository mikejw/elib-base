<?php

declare(strict_types=1);

use Empathy\ELib\File\GImage;
use Empathy\MVC\Config;

describe('ELib File GImage', function () {
    $file = null;

    beforeEach(function () use (&$file) {
        $base = dirname(__DIR__, 3);

        $_FILES = [
            'file' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => $base.'/tests/empathy.jpg',
                'error' => 0,
            ],
        ];

        $fakeDocRoot = $base.'/tests/tmp';
        $uploadsDir = $fakeDocRoot.'/public_html/uploads';
        if (! is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        Config::store('DOC_ROOT', $fakeDocRoot);
        $file = new GImage('', false, []);
    });

    test('upload', function () use (&$file) {
        $file->upload();
        expect($file->getError())->toBe('');
        expect($file->remove([$file->filename]))->toBe(1);
    });

    test('derived', function () use (&$file) {
        $file->upload();
        $deriv = [
            ['l_', 800, 600],
            ['tn_', 200, 200],
            ['mid_', 500, 500],
        ];

        $file->create();
        foreach ($deriv as $item) {
            $file->makeDerived($item[0], $item[1], $item[2]);
        }
        $file->destroy();

        expect($file->remove([$file->filename]))->toBe(4);
    });
});
