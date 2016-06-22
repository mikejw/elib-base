<?php

namespace ESuite\Country;

use ESuite\ESuiteTest;


class CountryTest extends ESuiteTest
{
    private $country;
    

    protected function setUp()
    {
        $this->country = new \Empathy\ELib\Country\Country();
    }

    public function testCountries()
    {
    	$countries = $this->country->build();
    	$this->assertEquals($countries['GB'], 'United Kingdom');
    }


}