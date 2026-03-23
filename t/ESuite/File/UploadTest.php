<?php

namespace ESuite;

use Empathy\MVC\Config;
use Empathy\MVC\Util\Testing\ESuiteTestCase;

class UploadTest extends ESuiteTestCase
{
    private $file;
    

    protected function setUp(): void
    {
       
      
        // dummy upload
        $_FILES = array(
            'file' => array(
                'name' => 'new_one.mp3',
                'type' => 'audio/mpeg',
                'size' => 542,
                'tmp_name' => dirname(realpath(__FILE__)).'/../../new_one.mp3',
                'error' => 0
            )
        );

        // fake doc root
        Config::store('DOC_ROOT', dirname(realpath(__FILE__)).'/../../tmp');

        $this->file = new \Empathy\ELib\File\Upload(false);
    }


    public function testUpload()
    {
        $this->file->upload();
        $this->assertEquals("", $this->file->getError());
        $this->assertEquals(1, $this->file->remove(array($_FILES['file']['name'])));
    }

}
