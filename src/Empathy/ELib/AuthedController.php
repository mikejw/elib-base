<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\DI;

/*
  user has logged in to be here...
*/

class AuthedController extends EController
{
    public function __construct(Bootstrap $boot)
    {
        parent::__construct($boot);
        if (!DI::getContainer()->get('CurrentUser')->loggedIn()) {
            $this->authFailed();
        }
    }

    protected function authFailed(): void
    {
        $this->redirect('');
    }

}
