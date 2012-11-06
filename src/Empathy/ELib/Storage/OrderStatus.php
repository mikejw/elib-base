<?php

namespace Empathy\ELib\Storage;

use Empathy\MVC\Entity;

class OrderStatus extends Entity
{
    const TABLE = 'order_status';

    public $id;
    public $user_id;
    public $status;
    public $stamp;

}
