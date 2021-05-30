<?php

namespace App\Util;


class IdUtil
{
    //陣列變成字串
    public static function getId32(){
        $toFill = "00000000000000000000000000000000";
        $systemTime = dechex(time() - 1325347200);
        $num_bytes= rand(5, 10);
        $random = bin2hex(openssl_random_pseudo_bytes($num_bytes));
        $index = 32 - strlen ($systemTime) - strlen ($random);
        $id = $systemTime . substr($toFill, 0, $index) . $random;
        return substr($id, 0, 32);
    }
}