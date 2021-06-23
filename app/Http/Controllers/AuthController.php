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
use App\Http\Resources\VerificationCodeResource;
use App\Formatter\Constants;

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
     *isAccountExit(檢查是否註冊)
     * @response scenario="success"
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": ""
     * }
     * @response scenario="account is exit"
     * {
     *     "statusCode": "916",
     *     "msg": "帳號已經被註冊",
     *     "data": ""
     * } 
     */
    public function isAccountExit($account)
    {
        $member = $this->authService->isAccountExit($account);
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        else  return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
    } 
    /**
     *register(註冊)
     *@bodyParam name String 姓名
     *@bodyParam account String 帳號(Email)
     *@bodyParam password String 密碼
     * @response scenario="success"
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "token": "11d3e1a1000000d6f1a184a8b5219092",
     *         "memberId": 6,
     *         "expiryTime": "2021-06-30T10:55:29.000000Z"
     *     }
     * }
     * @response scenario="password is wrong format"
     * {
     *     "statusCode": "915",
     *     "msg": "密碼格式錯誤",
     *     "data": ""
     * }
     * @response scenario="account is exit"
     * {
     *     "statusCode": "916",
     *     "msg": "帳號已經被註冊",
     *     "data": ""
     * }
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        $validate = $this->authValidator->register($request);
        if($validate) return $validate;
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
     *login(登入)
     *@bodyParam account String 帳號(Email)
     *@bodyParam password String 密碼
     * @response scenario="success" 
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "token": "11cbc1790000000000990a802488c02f",
     *         "memberId": 5,
     *         "expiryTime": "2021-06-24 15:00:09"
     *     }
     * }
     * @response scenario="account or password is error"
     * {
     *     "statusCode": "917",
     *     "msg": "帳號或密碼錯誤",
     *     "data": ""
     * }
     * @param  mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        $validate = $this->authValidator->login($request);
        if($validate) return $validate;
        $member = $this->authService->login($request);
        /**找不到會員 帳密錯誤**/
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_MSG);
        $token = $this->memberTokenService->getTokenByMemberId($member->id);
        /**token 失效**/
        if($token == null) $token = $this->memberTokenService->createToken($member->id);
        $dataJson = new AuthResource($token);
        $this->logService = new ActionSucessLogService('AuthController', $member->id, 'login');
        $this->logService->printLog();
        return ResponseFormatter::jsonFormate($dataJson , ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    /**
     *sendForgetPasswordMail(寄忘記密碼驗證信)
     *@bodyParam account String 帳號(Email)
     *@response scenario="success" 
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "memberId": 5,
     *         "code": "448566",
     *         "status": null,
     *         "type": "002"
     *     }
     * }
     * @param  mixed $request
     * @return void
     */
    public function sendForgetPasswordVerificationCode(Request $request){
        $validate = $this->authValidator->sendForgetPasswordVerificationCode($request);
        if($validate) return $validate;
        $account = $request->input('account');
        $verificationCode = $this->authService->sendForgetPasswordVerificationCode($account, Constants :: $FORGET_PASSWORD_VERIFICATION_CODE);
        if(!$verificationCode) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_CODE, ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_MSG);
        $dataJson = new VerificationCodeResource($verificationCode);
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }    
    /**
    *checkForgetPasswordCode(忘記密碼的驗證碼是否存在)
    *@bodyParam account String 帳號(Email)
    *@bodyParam verificationCode String 驗證碼
    *@response scenario="success" 
    *{
    *   "statusCode": "0",
    *    "msg": "呼叫成功",
    *    "data": {
    *        "memberId": 1,
    *        "code": "123456",
    *        "status": 0,
    *        "type": "002"
    *    }
    *}
    * @param  mixed $request
    * @return void
    */
    public function forgetPasswordVerificationCodeIsExit(Request $request){
        $validate = $this->authValidator->forgetPasswordVerificationCodeIsExit($request);
        if($validate) return $validate;
        $account = $request->input('account');
        $code = $request->input('verificationCode'); 
        $verificationCode = $this->authService->forgetPasswordVerificationCodeIsExit($account, $code,Constants :: $FORGET_PASSWORD_VERIFICATION_CODE);
        if(!$verificationCode) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_MSG);
        $dataJson = new VerificationCodeResource($verificationCode);
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
