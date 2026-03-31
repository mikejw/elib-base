<?php

declare(strict_types=1);

namespace Empathy\ELib\Storage;

use Empathy\ELib\Country\Country;
use Empathy\ELib\Storage\ShippingAddress as EShippingAddress;
use Empathy\MVC\Entity;
use Empathy\MVC\Model;
use Empathy\MVC\Validate;

class ShippingAddress extends Entity
{
    public const TABLE = 'shippingaddr';

    public int $id;

    public mixed $user_id = null;

    public mixed $first_name = null;

    public mixed $last_name = null;

    public mixed $address1 = null;

    public mixed $address2 = null;

    public mixed $city = null;

    public mixed $state = null;

    public mixed $zip = null;

    public mixed $country = null;

    public mixed $default_address = null;

    public function validates(): void
    {
        if ($this->doValType(Validate::TEXT, 'first_name', $this->first_name, false)) {
            if ($this->first_name === 'Not provided') {
                $this->addValError('This is a required field', 'first_name');
            }
        }
        if ($this->doValType(Validate::TEXT, 'last_name', $this->last_name, false)) {
            if ($this->last_name === 'Not provided') {
                $this->addValError('This is a required field', 'last_name');
            }
        }
        $this->doValType(Validate::TEXT, 'address1', $this->address1, false);
        $this->doValType(Validate::TEXT, 'address2', $this->address2, true);
        $this->doValType(Validate::TEXT, 'city', $this->city, false);
        $this->doValType(Validate::TEXT, 'state', $this->state, false);
        $this->doValType(Validate::TEXT, 'zip', $this->zip, false);
        if ($this->doValType(Validate::TEXT, 'country', $this->country, false)) {
            if (!in_array($this->country, array_keys(Country::build()), true)) {
                $this->addValError('Not a valid country', 'country');
            }
        }
    }

    public function setDefault(int $user_id, int $address_id): void
    {
        $params = [];
        $sql = 'SELECT id FROM '.Model::getTable(EShippingAddress::class).' WHERE user_id = ?';
        $params[] = $user_id;
        $error = 'Could not get all shipping addresses for user.';
        $result = $this->query($sql, $error, $params);

        $addresses = [];
        foreach ($result as $row) {
            array_push($addresses, $row['id']);
        }

        if (in_array($address_id, $addresses, true)) {
            $params = [];
            $sql = 'UPDATE '.Model::getTable(EShippingAddress::class)
                .' SET default_address = 0 WHERE user_id = ?';
            $error = 'Could not wipe defaults.';
            $params[] = $user_id;
            $this->query($sql, $error, $params);
            $params = [];
            $sql = 'UPDATE '.Model::getTable(EShippingAddress::class)
                .' SET default_address = 1 WHERE id = ?';
            $params[] = $address_id;
            $error = 'Could not set new default';
            $this->query($sql, $error, $params);
        }
    }

    public function getDefault(int $user_id): int
    {
        $id = 0;
        $params = [];
        $sql = 'SELECT id FROM '.Model::getTable(EShippingAddress::class).' WHERE user_id = ?'
            .' AND default_address = 1';
        $params[] = $user_id;
        $error = 'Could not get defaut shipping address.';
        $result = $this->query($sql, $error, $params)->fetchAll();

        if (count($result) > 0) {
            $id = $result[0]['id'];
        }
        return $id;
    }
}
