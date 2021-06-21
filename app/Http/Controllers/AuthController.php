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
use App\Services\Log\LogService;
use App\Services\Log\ActionSucessLogService;

class AuthController extends Controller
{   
    private $authService;
    private $authValidator;
    private $memberTokenService;
    private $logService;

    public function __construct(
        AuthService $authService,
        AuthValidator $authValidator,
        MemberTokenService $memberTokenService
    )
    {
        $this->authService = $authService;
        $this->authValidator = $authValidator;
        $this->memberTokenService = $memberTokenService;
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
        $member = $this->authService->register($request);
        if($member == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
        $token = $this->memberTokenService->createToken($member->id);
        if($token == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $dataJson = new AuthResource($token);
        $this->logService = new ActionSucessLogService('AuthController', $member->id, 'register');
        $this->logService->printLog();
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    /**
     * 檢查是否註冊
     */
    public function isAccountExit($account)
    {
        $member = $this->authService->isAccountExit($account);
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        else  return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
    }    
    /**
     * 登入
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
        $this->logService = new ActionSucessLogService('AuthController', $member->id, 'login');
        $this->logService->printLog();
        return ResponseFormatter::jsonFormate($dataJson , ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
