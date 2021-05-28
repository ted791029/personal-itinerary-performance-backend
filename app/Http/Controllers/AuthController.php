<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Validator\AuthValidator;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\AuthResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Services\MemberService;
use App\Http\Resources\VerificationCodeResource;

class AuthController extends Controller
{   
    private $authService;
    private $authValidator;
    private $memberService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->authValidator = new AuthValidator();
        $this->memberService = new MemberService();
    }

    /**
     * 註冊
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        $validate = $this->authValidator->register($request);
        if($validate != null) return $validate;
        $dataJson = new AuthResource($this->authService->register($request));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    /**
     * 檢查是否註冊
     */
    public function isAccountExit($account)
    {
        $member = $this->memberService->getByAccount($account);
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        else  return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
    }
    /**
     * 寄出驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode(Request $request)
    {
        $validate = $this->authValidator->sendVerificationCode($request);
        if($validate != null) return $validate;
        $dataJson = new VerificationCodeResource($this->authService->sendVerificationCode($request));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
