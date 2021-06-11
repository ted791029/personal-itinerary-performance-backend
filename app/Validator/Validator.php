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
    protected function validateInputs($request, $keys){
        $inputs = $request->input();
        if(sizeof($inputs) != sizeof($keys)) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        for($i = 0; $i < sizeof($keys); $i++){
            $key = $keys[$i];
            if($request->input($key) == "") return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        }
    }
}


?>