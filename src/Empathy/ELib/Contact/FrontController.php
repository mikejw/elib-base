<?php

namespace Empathy\ELib\Contact;
use Empathy\ELib\Config;
use Empathy\ELib\EController;
use Empathy\ELib\Mailer;
use Empathy\MVC\Model;
use Empathy\MVC\Entity;
use Empathy\MVC\DI;

class FrontController extends EController
{

    public function default_event()
    {
        $service =  DI::getContainer()->get('Contact');
        $this->initID('id', 1, true);
        if (isset($_POST['submit'])) {
            if (!$service->signUp()) {
                $this->presenter->assign('errors', $service->getErrors());
            } else {
                $service->persist();
                $this->redirect('contact/thanks/1');
            }

        } elseif (isset($_POST['submit_msg'])) {

            if (!$service->email()) {
                $this->presenter->assign('errors', $service->getErrors());
            } else {
                $service->sendEmail();
                $service->persist();
                $this->redirect('contact/thanks/2');
            }
        }
        $this->assign('contact', $service->getContact());
        $this->assign('contact_type_id', $_GET['id']);
        $this->setTemplate('elib://contact.tpl');
    }

    public function thanks()
    {
        $this->initID('id', 0, true);
        $this->assign('thanks_id', $_GET['id']);
        $this->setTemplate('elib://contact.tpl');
    }
}