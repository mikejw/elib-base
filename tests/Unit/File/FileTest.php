<?php

declare(strict_types=1);

use Empathy\ELib\File;
use Empathy\MVC\Config;

describe('ELib File', function () {
    beforeEach(function () {
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
    });

    test('upload', function () {
        $file = new File('', false, []);
        $file->upload();
        expect($file->getError())->toBe('');
        expect($file->remove([$file->filename]))->toBe(1);
    });

    test('derived', function () {
        $file = new File('', false, []);
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
