<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\MemberTokenService;
use App\Validator\AuthValidator;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\AuthResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Services\MemberService;

class AuthController extends Controller
{   
    private $authService;
    private $authValidator;
    private $memberService;
    private $memberTokenService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->authValidator = new AuthValidator();
        $this->memberService = new MemberService();
        $this->memberTokenService = new MemberTokenService();
    }

    /**
     * 註冊
     *@bodyParam name String 姓名
     *@bodyParam account String 帳號(Email)
     *@bodyParam password String 密碼
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
     * login
     *@bodyParam account String 帳號(Email)
     *@bodyParam password String 密碼
     * @param  mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        $validate = $this->authValidator->login($request);
        if($validate != null) return $validate;
        $member = $this->authService->login($request);
        //找不到會員 帳密錯誤
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_MSG);
        $token = $this->memberTokenService->getTokenByMemberId($member->id);
        //token 失效
        if($token == null) $token = $this->memberTokenService->createToken($member->id);
        $dataJson = new AuthResource($token);
        return ResponseFormatter::jsonFormate($dataJson , ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
