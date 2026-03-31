<?php

declare(strict_types=1);

namespace Empathy\ELib\Contact;

use Empathy\ELib\EController;
use Empathy\MVC\DI;

class FrontController extends EController
{
    public function default_event(): void
    {
        $service =  DI::getContainer()->get('Contact');
        $this->initID('id', 1, true);
        if (isset($_POST['submit'])) {
            if (!$service->signUp()) {
                $this->assign('errors', $service->getErrors());
            } else {
                $service->persist();
                $this->redirect('contact/thanks/1');
            }

        } elseif (isset($_POST['submit_msg'])) {

            if (!$service->email()) {
                $this->assign('errors', $service->getErrors());
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

    public function thanks(): void
    {
        $this->initID('id', 0, true);
        $this->assign('thanks_id', $_GET['id']);
        $this->setTemplate('elib://contact.tpl');
    }
}
