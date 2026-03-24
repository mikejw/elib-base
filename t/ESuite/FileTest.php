<?php

namespace ESuite;

use Empathy\MVC\Config;
use Empathy\MVC\Util\Testing\ESuiteTestCase;

class FileTest extends ESuiteTestCase
{
    private $file;
    

    protected function setUp(): void
    {

        //$this->markTestSkipped();

        // dummy upload
        $_FILES = array(
            'file' => array(
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(realpath(__FILE__)).'/../empathy.jpg',
                'error' => 0
            )
        );

        // fake doc root
        $fakeDocRoot = dirname(realpath(__FILE__)) . '/../../tmp';
        $uploadsDir = $fakeDocRoot . '/public_html/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        Config::store('DOC_ROOT', $fakeDocRoot);

        $this->file = new \Empathy\ELib\File('', false, array());
    }

    public function testUpload()
    {
        $this->file->upload();
        $this->assertEquals("", $this->file->getError());
        $this->assertEquals(1, $this->file->remove([$this->file->filename]));
    }

   

    public function testDerived()
    {
        $this->file->upload();
        $deriv = array(
            array('l_', 800, 600),
            array('tn_', 200, 200),
            array('mid_', 500, 500)
        );
        
        $this->file->create();
        foreach ($deriv as $item) {
            $this->file->makeDerived($item[0], $item[1], $item[2]);
        }
        $this->file->destroy();
        $this->assertEquals(4, $this->file->remove([$this->file->filename]));
    }
}