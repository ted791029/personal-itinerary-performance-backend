<?php

namespace App\Validator;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Validator\Validator;

class AuthValidator
{
    private $memberService;

    public function __construct()
    {
        $this->memberService = new MemberService();
    }

    /**
     * 註冊驗證
     */
    public function register(Request $request){
        $inputValidate = Validator::validateInputs($request, 3);
        if($inputValidate != null) return $inputValidate;
        $account = $request->input('account');
        $password = $request->input('password');
        $reg = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/";
        if(!(preg_match( $reg, $password))) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        $member = $this->memberService->getByAccount($account);
        if($member != null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);;
    }
    /**
     * 產生驗證碼驗證
     */
    public function sendVerificationCode(Request $request){
        $inputValidate = Validator::validateInputs($request, 1);
        if($inputValidate != null) return $inputValidate;
    }
}