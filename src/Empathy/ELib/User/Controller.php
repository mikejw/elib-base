<?php

declare(strict_types=1);

namespace Empathy\ELib\User;

use Empathy\ELib\Country\Country;
use Empathy\ELib\EController;
use Empathy\MVC\Bootstrap;
use Empathy\MVC\DI;
use Empathy\MVC\Session;

class Controller extends EController
{
    private CurrentUser $currentUser;

    protected mixed $userModel;

    public function __construct(Bootstrap $boot)
    {
        parent::__construct($boot);
        $this->currentUser = DI::getContainer()->get('CurrentUser');
        $this->userModel = DI::getContainer()->get('UserModel');
    }

    public function default_event(): void
    {
        $this->redirect('');
    }


    public function login(): void
    {
        $this->assign('centerpage', true);
        $this->setTemplate('elib:/login.tpl');
        $errors = [];

        if (isset($_POST['login']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('csrf_token')) {
            list($errors, $user) = $this->currentUser->doLogin($_POST['username'], $_POST['password'], true, $this->userModel);

            if (!sizeof($errors)) {
                if ($this->currentUser->isAdmin($user)) {
                    $this->redirect('admin');
                } else {
                    $this->redirect('');
                }
            } else {
                $this->assign('errors', $user->getValErrors());
                $this->assign('username', $_POST['username']);
                $this->assign('password', $_POST['password']);
            }
        }
        $this->assignCSRFToken();
    }

    public function logout(): void
    {
        if (!$this->currentUser->doLogout()) {
            throw new \Exception('Could not logout');
        }
        $this->redirect('');
    }

    private function nullify(mixed &$var): void
    {
        if (isset($var) && $var === '') {
            $var = null;
        }
    }

    public function register(): void
    {
        $errors = [];
        $submitted = false;
        $supply_address = false;

        if (isset($_POST['submit'])) {
            $submitted = true;
            $this->nullify($_POST['first_name']);
            $this->nullify($_POST['last_name']);

            $_POST['first_name'] = $_POST['first_name'] ?? 'Not provided';
            $_POST['last_name'] = $_POST['last_name'] ?? 'Not provided';

            $supply_address = (isset($_POST['supply_address']) && $_POST['supply_address'] === 'on') ? true : false;

            $username = $_POST['username'] ?? '';
            $username = strtolower($username);
            $email = $_POST['email'] ?? '';
            $fullname = $_POST['fullname'] ?? '';
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $address1 = $_POST['address1'] ?? '';
            $address2 = $_POST['address2'] ?? '';
            $city = $_POST['city'] ?? '';
            $state = $_POST['state'] ?? '';
            $zip = $_POST['zip'] ?? '';
            $country = $_POST['country'] ?? '';

            list($errors, $user, $address) = $this->currentUser->doRegister(
                $supply_address,
                $username,
                $email,
                $fullname,
                $first_name,
                $last_name,
                $address1,
                $address2,
                $city,
                $state,
                $zip,
                $country
            );

            if (!sizeof($errors)) {
                $this->redirect('user/thanks/1');
            } else {
                $address->first_name = (
                    isset($address->first_name) &&
                    $address->first_name === 'Not provided'
                ) ? '' : $address->first_name ?? '';
                $address->last_name = (
                    isset($address->last_name) &&
                    $address->last_name === 'Not provided'
                ) ? '' : $address->last_name ?? '';

                $this->assign('user', $user);
                $this->assign('address', $address);
            }
        }

        $titles = ['Mr', 'Mrs', 'Miss', 'Ms', 'Dr'];
        $countries = Country::build();
        $this->assign('errors', $errors);
        $this->assign('titles', $titles);
        $this->assign('countries', $countries);
        $this->assign('sc', ($submitted && $supply_address) ? $address->country : 'GB');
        $this->setTemplate('elib://register.tpl');
        $this->assign('submitted', $submitted);
        $this->assign('supply_address', $supply_address);
    }

    public function confirm_reg(): void
    {
        $reg_code = $_GET['code'];
        if ($this->currentUser->doConfirmReg($reg_code)) {
            $this->redirect('user/thanks/2');
        } else {
            throw new \Exception('Unable to activate user.');
        }
    }

    public function thanks(): void
    {
        $this->assign('id', $_GET['id']);
        $this->setTemplate('elib://thanks.tpl');
    }

}
