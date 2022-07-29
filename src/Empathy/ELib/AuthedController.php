<?php

namespace Empathy\ELib;
use Empathy\MVC\DI;


/*
  user has logged in to be here...
*/

class AuthedController extends EController
{
    public function __construct($boot)
    {
        parent::__construct($boot);
        if (!DI::getContainer()->get('CurrentUser')->loggedIn()) {
            $this->authFailed();
        }
    }

    protected function authFailed()
    {       
        $this->redirect('');
    }

}
