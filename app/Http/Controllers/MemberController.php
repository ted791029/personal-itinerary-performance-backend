<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MmeberResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Services\MemberService;
use App\Validator\MemberValidator;
use App\Services\MemberTokenService;
use App\Http\Resources\VerificationCodeResource;
use App\Services\Log\LogService;
use App\Services\Log\ActionSucessLogService;
use App\Formatter\Constants;

class MemberController extends Controller
{
    private $memberService;
    private $memberTokenService;
    private $memberValidator;
    private $logService;

    public function __construct(
        MemberService $memberService,
        MemberTokenService $memberTokenService,
        MemberValidator $memberValidator
    )
    {
        $this->memberService = $memberService;
        $this->memberTokenService = $memberTokenService;
        $this->memberValidator = $memberValidator;
    }
    
    /**
     * getMemberByToken(依照token 取得會員)
     *@response scenario="success"
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "id": 5,
     *         "name": "Ted",
     *         "account": "azoocx791029@gmail.com",
     *         "verifyStatus": 1
     *     }
     * }
     * @response scenario="error token"
     * {
     *     "statusCode": "914",
     *     "msg": "無效token",
     *     "data": ""
     * }
     * @param  mixed $id
     * @return void
     */
    public function getByToken($memberToken)
    {
        $token = $this->memberTokenService->getToken($memberToken);
        if(!$token) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $dataJson = new MmeberResource($this->memberService->getById($token->memberId));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }

    /**
     * sendVerificationMail(寄出驗證碼)
     * @bodyParam memberToken String 唯一辨識碼
     * @response scenario="success"
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "memberId": 5,
     *         "code": "123456",
     *         "status": null,
     *         "type": "001"
     *     }
     * }
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode(Request $request)
    {
        $validate = $this->memberValidator->sendVerificationCode($request);
        if($validate) return $validate;
        $token = $this->memberTokenService->getToken($request->input('memberToken'));
        if(!$token) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $verificationCode = $this->memberService->sendVerificationCode($token->memberId, Constants :: $MEMBER_VERIFICATION_CODE);
        if(!$verificationCode) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_CODE, ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_MSG);
        $dataJson = new VerificationCodeResource($verificationCode);
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    
    /**
     *MemberVerify(驗證)
     *@bodyParam memberToken String 唯一辨識碼
     *@bodyParam verificationCode String 驗證碼
     *@response scenario="success"
     * {
     *     "statusCode": "0",
     *     "msg": "呼叫成功",
     *     "data": {
     *         "id": 5,
     *         "name": "Ted",
     *         "account": "azoocx791029@gmail.com",
     *         "verifyStatus": 1
     *     }
     * }
     *@response scenario="verification code error"
     * {
     *     "statusCode": "918",
     *     "msg": "無效驗證碼",
     *     "data": ""
     * }
     * @param  mixed $request
     * @return void
     */
    public function verify(Request $request)
    {
        $validate = $this->memberValidator->verify($request);
        if($validate) return $validate;
        $token = $this->memberTokenService->getToken($request->input('memberToken'));
        if(!$token) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $member = $this->memberService->verify($token->memberId, $request->input('verificationCode'), Constants :: $MEMBER_VERIFICATION_CODE);
        if($member == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_MSG);
        $dataJson = new MmeberResource($member);
        $this->logService = new ActionSucessLogService('MemberController', $member->id, 'verify');
        $this->logService->printLog();
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
