<?php

namespace ESuite;

use ESuite\ESuiteTest;
use Empathy\MVC\Config;

class GImageTest extends ESuiteTest
{
    private $file;
    

    protected function setUp()
    {
       
      
        // dummy upload
        $_FILES = array(
            'file' => array(
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => dirname(realpath(__FILE__)).'/../../empathy.jpg',
                'error' => 0
            )
        );

        // fake doc root
        Config::store('DOC_ROOT', dirname(realpath(__FILE__)).'/../../tmp');

        $this->file = new \Empathy\ELib\File\GImage('', false, array());
    }


    public function testUpload()
    {
        $this->file->upload();
        $this->assertEquals("", $this->file->getError());
        $this->assertEquals(1, $this->file->remove(array($_FILES['file']['name'])));
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
        
        $this->assertEquals(4, $this->file->remove(array($_FILES['file']['name'])));
    }

}