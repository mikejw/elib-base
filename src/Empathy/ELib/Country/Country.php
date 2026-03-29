<?php

declare(strict_types=1);

namespace Empathy\ELib\Country;

define('SOURCE', dirname(realpath(__FILE__)).'/countries.html');

class Country
{
    private static $europeanCountryCodes = [
        'AL', // Albania
        'AD', // Andorra
        'AM', // Armenia
        'AT', // Austria
        'AZ', // Azerbaijan
        'BY', // Belarus
        'BE', // Belgium
        'BA', // Bosnia and Herzegovina
        'BG', // Bulgaria
        'HR', // Croatia
        'CY', // Cyprus
        'CZ', // Czechia
        'DK', // Denmark
        'EE', // Estonia
        'FI', // Finland
        'FR', // France
        'GE', // Georgia
        'DE', // Germany
        'GR', // Greece
        'HU', // Hungary
        'IS', // Iceland
        'IE', // Ireland
        'IT', // Italy
        'XK', // Kosovo (not ISO 3166-1 official, include only if you want it)
        'LV', // Latvia
        'LI', // Liechtenstein
        'LT', // Lithuania
        'LU', // Luxembourg
        'MT', // Malta
        'MD', // Moldova
        'MC', // Monaco
        'ME', // Montenegro
        'NL', // Netherlands
        'MK', // North Macedonia
        'NO', // Norway
        'PL', // Poland
        'PT', // Portugal
        'RO', // Romania
        'RU', // Russia
        'SM', // San Marino
        'RS', // Serbia
        'SK', // Slovakia
        'SI', // Slovenia
        'ES', // Spain
        'SE', // Sweden
        'CH', // Switzerland
        'TR', // Türkiye
        'UA', // Ukraine
        'GB', // United Kingdom
        'VA', // Holy See
    ];

    public static function isEurope($code)
    {
        return in_array($code, self::$europeanCountryCodes, true);
    }

    public static function build()
    {
        //$pathToEmp = explode('empathy', __FILE__);
        //if(($fp = @fopen($pathToEmp[0].SOURCE, 'r')) == false)
        if (($fp = @fopen(SOURCE, 'r')) === false) {
            echo 'Could not open source file.';
        } else {
            $i = 0;
            $j = 0;
            $k = 1;
            while (($line = fgets($fp))) {
                if (!(
                    preg_match('/^<table/', $line)
                       ||
                       preg_match('/<tr>/', $line)
                       ||
                       preg_match('/<\/td>/', $line)
                       ||
                       preg_match('/<\/tr>/', $line)
                       ||
                       preg_match('/<td/', $line)
                       ||
                       preg_match('/<tr/', $line)
                       ||
                       preg_match('/<\/table/', $line)
                       ||
                       preg_match('/^\n/', $line)
                       ||
                       preg_match('/\ see\ /', $line)
                       ||
                       preg_match('/\t\t/', $line)
                )) {
                    $format = strip_tags($line);

                    if ((($k + 1) % 2) === 0) {
                        $format = strtolower($format);
                        $format_arr = explode(' ', $format);
                        for ($l = 0; $l < sizeof($format_arr); $l++) {
                            if ($format_arr[$l] !==  'and') {
                                $format_arr[$l] = ucfirst($format_arr[$l]);
                            }
                        }
                        $format = implode(' ', $format_arr);
                        $format = str_replace('\n', '', $format);
                        $format = preg_replace('/ $/', '', $format);
                        $format = preg_replace('/^ */', '', $format);
                        $country['name'][$j] = $format;
                        $k++;
                    } else {
                        $format = str_replace(' ', '', $format);
                        $format = str_replace('\n', '', $format);
                        $country['code'][$j] = $format;
                        $j++;
                        $k++;
                    }
                }
                $i++;
            }
            fclose($fp);
        }

        foreach ($country['code'] as $index => $value) {
            $built[preg_replace('/[^\w]/', '', $value)] = trim($country['name'][$index]);
        }
        return $built;
    }
}
