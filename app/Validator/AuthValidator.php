<?php

namespace App\Validator;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Validator\Validator;

class AuthValidator extends Validator 
{
    private $memberService;


    public function __construct(
        MemberService $memberService
    )
    {
        $this->memberService = $memberService;
    }
    
    /**
     * 註冊驗證
     */
    public function register(Request $request){
        $keys = ['name', 'account', 'password'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
        $account = $request->input('account');
        $password = $request->input('password');
        $reg = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/";
        if(!(preg_match( $reg, $password))) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        $member = $this->memberService->getByAccount($account);
        if($member != null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
    }
    /**
     * 登入驗證
    */
    public function login(Request $request){
        $keys = ['account', 'password'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
    }
    /**
     * 寄忘記密碼驗證信驗證
     *
     * @param  mixed $request
     * @return void
     */
    public function sendForgetPasswordVerificationCode(Request $request){
        $keys = ['account'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
        $account = $request->input('account');
        $member = $this->memberService->getByAccount($account);
        if($member == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
    }
    /**
     * 檢查忘記密碼驗證碼驗證
     *
     * @param  mixed $request
     * @return void
     */
    public function forgetPasswordVerificationCodeIsExit(Request $request){
        $keys = ['account', 'verificationCode'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
        $account = $request->input('account');
        $member = $this->memberService->getByAccount($account);
        if($member == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
    }
}