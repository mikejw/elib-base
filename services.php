<?php

use Empathy\ELib\User\CurrentUser;
use Empathy\ELib\Contact;


return [
    'UserModel' => 'UserItem',
    'CurrentUser' => function (\DI\Container $c) {
        return new CurrentUser();
    },
    'Contact' => function (\DI\Container $c) {
        return new Contact();
    }
];
