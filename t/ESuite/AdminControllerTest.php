<?php

namespace ESuite;

use Empathy\MVC\Util\Testing\ESuiteTestCase;
use Empathy\MVC\DI;

class AdminControllerTest extends ESuiteTestCase
{
 
 	private $bootstrap;


	public function setup(): void {
 		parent::setUp();
        $this->bootstrap = $this->makeFakeBootstrap(\Empathy\MVC\Plugin\ELibs::TESTING_LIB);
	}


	public function testInit() {

		$this->expectOutputRegex('/session start/');

		// fake the module 
		$_GET['module'] = 'default_event';
		$this->bootstrap->dispatch(true, '\Empathy\ELib\AdminController');

		$controller = DI::getContainer()->get('Controller');
		$this->assertInstanceOf('\Empathy\ELib\AdminController', $controller);
	}


	// public function testPassword() {
		
	// 	$this->expectOutputRegex('/session start/');

	// 	// fake the module
	// 	$_GET['module'] = 'default_event';
	// 	$this->bootstrap->dispatch(false, '\Empathy\ELib\AdminController');

	// 	$controller = $this->bootstrap->getController();

	// 	$_POST['submit'] = true;
	// 	$_POST['old_password'] = '';
	// 	$_POST['password1'] = '123';
	// 	$_POST['password2'] = '123';


	// 	$controller->password();

	// }

}