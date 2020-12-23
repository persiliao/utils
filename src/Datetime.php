<?php

declare(strict_types=1);

namespace PersiLiao\Utils;

use function date_default_timezone_set;
use function floor;
use function strtotime;
use function time;

class Datetime{

    public static function calculationElapsedTime(string $datetime, string $timezone = 'Asia/Shanghai', bool $echo = true)
    {
        date_default_timezone_set($timezone);
        $tip = $datetime;
        $t = time() - strtotime($tip);
        $f = [
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '个星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒',
        ];
        foreach($f as $k => $v){
            $c = (int)floor($t / (int)$k);
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
