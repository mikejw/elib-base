<?php

use Empathy\ELib\Contact;


return [
    'Contact' => function (\DI\Container $c) {
        return new Contact();
    }
];


