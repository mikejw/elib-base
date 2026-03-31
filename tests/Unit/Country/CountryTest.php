<?php

declare(strict_types=1);

use Empathy\ELib\Country\Country;

describe('ELib Country', function () {
    test('build maps GB to United Kingdom', function () {
        $countries = Country::build();
        expect($countries['GB'])->toBe('United Kingdom');
    });
});
