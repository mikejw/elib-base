<?php

declare(strict_types=1);

use Empathy\MVC\DI;
use Empathy\MVC\Plugin\ELibs;

beforeEach(function () {
    $this->bootstrap = $this->empathy->makeFakeBootstrap(ELibs::TESTING_LIB);
});

test('init dispatches AdminController', function () {
    ob_start();
    $_GET['module'] = 'default_event';
    $this->bootstrap->dispatch(true, \Empathy\ELib\AdminController::class);
    $output = (string) ob_get_clean();

    expect($output)->toMatch('/session start/');

    $controller = DI::getContainer()->get('Controller');
    expect($controller)->toBeInstanceOf(\Empathy\ELib\AdminController::class);
});
