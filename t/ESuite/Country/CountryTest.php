<?php

namespace ESuite\Country;

use Empathy\MVC\Util\Testing\ESuiteTestCase;

class CountryTest extends ESuiteTestCase
{
    private $country;
    

    protected function setUp(): void
    {
        $this->country = new \Empathy\ELib\Country\Country();
    }

    public function testCountries()
    {
    	$countries = $this->country->build();
    	$this->assertEquals($countries['GB'], 'United Kingdom');
    }
}