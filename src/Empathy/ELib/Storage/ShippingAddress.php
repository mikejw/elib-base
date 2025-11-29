<?php

namespace Empathy\ELib\Storage;

use Empathy\MVC\Model;
use Empathy\MVC\Entity;
use Empathy\MVC\Validate;
use Empathy\ELib\Country\Country;
use Empathy\ELib\Storage\ShippingAddress as EShippingAddress;


class ShippingAddress extends Entity
{
    const TABLE = 'shippingaddr';

    public $id;
    public $user_id;
    public $first_name;
    public $last_name;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $default_address;

    public function validates()
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
            if (!in_array($this->country, array_keys(Country::build()))) {
                $this->addValError('Not a valid country', 'country');
            }
        }
    }

    public function setDefault($user_id, $address_id)
    {
        $params = [];
        $sql = 'SELECT id FROM '.Model::getTable(EShippingAddress::class).' WHERE user_id = ?';
        $params[] = $user_id;
        $error = 'Could not get all shipping addresses for user.';
        $result = $this->query($sql, $error, $params);

        $addresses = array();
        foreach ($result as $row) {
            array_push($addresses, $row['id']);
        }

        if (in_array($address_id, $addresses)) {
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
    
    public function getDefault($user_id): int {
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