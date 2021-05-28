<?php
namespace App\Validator;

use Illuminate\Http\Request;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;

class Validator
{        
    /**
     * 檢驗多個輸入參數
     *
     * @param  mixed $inputs
     * @param  mixed $length
     * @return void
     */
    public static function validateInputs($request, $length){
        $inputs = $request->input();
        if(sizeof($inputs) != $length) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        foreach ($inputs as $key => $value) {
            if($inputs[$key] == "") return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        }
    }
}


?>