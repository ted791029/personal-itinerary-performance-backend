<?php

namespace App\Formatter;

class ResponseFormatter
{
    public static function jsonFormate($dataJson, $statusCode, $msg){
        $tempArr =
        [
            'statusCode' => $statusCode,
            'msg' => $msg,
            'data' => $dataJson
        ];
        return Response()->json($tempArr);
    }
}
?>