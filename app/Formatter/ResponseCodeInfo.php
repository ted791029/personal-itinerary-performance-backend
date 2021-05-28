<?php
namespace App\Formatter;
class ResponseCodeInfo{
    static public $RESPONSE_SUCESS_CODE = "0";
    static public $RESPONSE_SUCESS_MSG = "呼叫成功";
    static public $RESPONSE_ERROR_CODE = "911";
    static public $RESPONSE_ERROR_MSG = "發生錯誤"; 
    static public $RESPONSE_PARAM_ERROR_CODE = "912";
    static public $RESPONSE_PARAM_ERROR_MSG = "傳入參數空值";
    static public $RESPONSE_PARAM_LENGTH_ERROR_CODE = "913";
    static public $RESPONSE_PARAM_LENGTH_ERROR_MSG = "傳入參數數量錯誤";

    //Member
    static public $RESPONSE_MEMBER_PASSWORD_ERROR_CODE = "914";
    static public $RESPONSE_MEMBER_PASSWORD_ERROR_MSG = "密碼格式錯誤";
    static public $RESPONSE_MEMBER_ISREGISTER_ERROR_CODE = "915";
    static public $RESPONSE_MEMBER_ISREGISTER_ERROR_MSG = "帳號已經被註冊";
}

?>