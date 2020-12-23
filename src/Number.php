<?php

declare(strict_types=1);

namespace PersiLiao\Utils;

use function floor;

class Number{

    public static function calculationElapsed(int $number, bool $echo = true)
    {
        $tip = $number;
        $f = [
            '10000000' => 'KW',
            '10000' => 'W',
            '1000' => 'K',
        ];
        foreach($f as $k => $v){
            $c = (int)floor($number / (int)$k);
            if(0 !== $c){
                $tip = $c . $v;
                break;
            }
        }
        if($echo){
            echo $tip;
        }else{
            return $tip;
        }
    }

}
