<?php

namespace App\Helpers;

class AadhaarHelper
{
    private static $d = [
        [0,1,2,3,4,5,6,7,8,9],
        [1,2,3,4,0,6,7,8,9,5],
        [2,3,4,0,1,7,8,9,5,6],
        [3,4,0,1,2,8,9,5,6,7],
        [4,0,1,2,3,9,5,6,7,8],
        [5,9,8,7,6,0,4,3,2,1],
        [6,5,9,8,7,1,0,4,3,2],
        [7,6,5,9,8,2,1,0,4,3],
        [8,7,6,5,9,3,2,1,0,4],
        [9,8,7,6,5,4,3,2,1,0]
    ];

    private static $p = [
        [0,1,2,3,4,5,6,7,8,9],
        [1,5,7,6,2,8,3,0,9,4],
        [5,8,0,3,7,9,6,1,4,2],
        [8,9,1,6,0,4,3,5,2,7],
        [9,4,5,3,1,2,6,8,7,0],
        [4,2,8,6,5,7,3,9,0,1],
        [2,7,9,3,8,0,6,4,1,5],
        [7,0,4,6,9,1,3,2,5,8]
    ];

    private static $inv = [0,4,3,2,1,5,6,7,8,9];

    public static function validate($aadhaar)
    {
        if (!ctype_digit($aadhaar) || strlen($aadhaar) !== 12) {
            return false; // must be 12 digits
        }

        $array = array_reverse(str_split($aadhaar));
        $c = 0;
        foreach ($array as $i => $digit) {
            $c = self::$d[$c][self::$p[$i % 8][$digit]];
        }

        return $c === 0;
    }
}
