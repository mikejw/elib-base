<?php

declare(strict_types=1);

namespace ESuite;

use Empathy\MVC\DI;
use Empathy\MVC\Util\Testing\ESuiteTestCase;
use Empathy\MVC\Bootstrap;
use Empathy\MVC\Plugin\ELibs;
use Empathy\ELib\User\CurrentUser;
use Empathy\ELib\Storage\UserItem;

class ESuiteTest extends ESuiteTestCase
{
    /*
    protected function makeFakeBootstrap($persistentMode = true): Bootstrap
    {
        // use eaa archive as root
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../eaa/');
        $container = DI::init($doc_root, $persistentMode);
        $empathy = $container->get('Empathy');
        $empathy->init();
        return $container->get('Bootstrap');
    }
    */

    protected function makeFakeBootstrap(int $testingMode = ELibs::TESTING_EMPATHY): Bootstrap
    {
        $b = parent::makeFakeBootstrap($testingMode);
        DI::getContainer()->set('UserModel', UserItem::class);
        DI::getContainer()->set('CurrentUser', new CurrentUser());

        return $b;
    }
}
