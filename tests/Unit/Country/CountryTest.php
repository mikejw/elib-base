<?php

declare(strict_types=1);

use Empathy\ELib\Country\Country;

describe('ELib Country', function () {
    $country = null;

    beforeEach(function () use (&$country) {
        $country = new Country();
    });

    test('build maps GB to United Kingdom', function () use (&$country) {
        $countries = $country->build();
        expect($countries['GB'])->toBe('United Kingdom');
    });
});
