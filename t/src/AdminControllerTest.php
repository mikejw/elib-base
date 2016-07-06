<?php

namespace ESuite;

use Empathy\MVC\Util\Testing\ESuiteTest;
use Empathy\MVC\Config;



class AdminControllerTest extends ESuiteTest
{
 
 	private $bootstrap;
 	private $controller;


	public function setup() {
 		parent::setUp();
        $this->bootstrap = $this->makeFakeBootstrap(\Empathy\MVC\Plugin\ELibs::TESTING_LIB);        
	}


	public function testInit() {

		$this->expectOutputRegex('/session start/');

		// fake the module 
		$_GET['module'] = 'default_event';
		$this->bootstrap->dispatch(false, '\Empathy\ELib\AdminController');

		$controller = $this->bootstrap->getController();
		$this->assertInstanceOf('\Empathy\ELib\AdminController', $controller);
	}


	public function testPassword() {
		
		$this->expectOutputRegex('/session start/');

		// fake the module
		$_GET['module'] = 'default_event';
		$this->bootstrap->dispatch(false, '\Empathy\ELib\AdminController');

		$controller = $this->bootstrap->getController();

		$_POST['submit'] = true;


		$controller->password();

	}

}