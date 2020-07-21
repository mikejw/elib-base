<?php

namespace Empathy\ELib\Contact;
use Empathy\ELib\Config;
use Empathy\ELib\EController;
use Empathy\ELib\Mailer;
use Empathy\ELib\Model;
use Empathy\MVC\Entity;

class FrontController extends EController
{

    public function default_event()
    {
        $c = Model::load('Contact');
        $this->initID('id', 1, true);

        if (isset($_POST['submit'])) {
            $c->assignFromPost(array('submitted', 'message', 'subject', 'body'));
            $c->message = 0;
            $c->submitted = 'MYSQLTIME';

            $c->validates();
            if ($c->hasValErrors()) {
                $this->presenter->assign('errors', $c->getValErrors());
            } else {
                $c->id = $c->insert(Model::getTable('Contact'), true, array(''), Entity::SANITIZE);
                $this->redirect('contact/thanks/1');
            }

        } elseif (isset($_POST['submit_msg'])) {
            $c->assignFromPost(array('message', 'submitted'));
            $c->message = 1;
            $c->submitted = 'MYSQLTIME';

            $c->validates();
            if ($c->hasValErrors()) {
                $this->presenter->assign('errors', $c->getValErrors());
            } else {
                // send email
                $r = array();
                if (
                    Config::get('EMAIL_ORGANISATION') &&
                    Config::get('EMAIL_FROM') &&
                    Config::get('EMAIL_RECIPIENT')
                ) {
                    $r[0]['alias'] = Config::get('EMAIL_RECIPIENT');
                    $r[0]['address'] = Config::get('EMAIL_RECIPIENT');

                    $messageOut = "Message sent from: "
                        .$c->first_name. ' ' .$c->last_name
                        ." - ".$c->email."<br /><br />"
                        .$c->body;

                    $m = new Mailer($r, $c->subject, $messageOut, null, true);
                }

                $c->id = $c->insert(Model::getTable('Contact'), true, array(''), Entity::SANITIZE);
                $this->redirect('contact/thanks/2');
            }
        }
        $this->assign('contact', $c);
        $this->presenter->assign('contact', $c);
        $this->presenter->assign('contact_type_id', $_GET['id']);
        $this->setTemplate('elib://contact.tpl');
    }

    public function thanks()
    {
        $this->initID('id', 0, true);
        $this->assign('thanks_id', $_GET['id']);
        $this->setTemplate('elib://contact.tpl');
    }
}