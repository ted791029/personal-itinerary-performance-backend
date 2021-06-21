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
     * 依照token 取得會員
     *
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
     * 寄出驗證碼
     *@bodyParam memberToken String 唯一辨識碼
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode(Request $request)
    {
        $validate = $this->memberValidator->sendVerificationCode($request);
        if($validate != null) return $validate;
        $token = $this->memberTokenService->getToken($request->input('memberToken'));
        if(!$token) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $verificationCode = $this->memberService->sendVerificationCode($token->memberId);
        if(!$verificationCode) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FAILED_SEND_VERIFICATION_CODE_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FAILED_SEND_VERIFICATION_CODE_MSG);
        $dataJson = new VerificationCodeResource($verificationCode);
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    
    /**
     *驗證
     *@bodyParam memberToken String 唯一辨識碼
     *@bodyParam verificationCode String 驗證碼
     * @param  mixed $request
     * @return void
     */
    public function verify(Request $request)
    {
        $validate = $this->memberValidator->verify($request);
        if($validate != null) return $validate;
        $token = $this->memberTokenService->getToken($request->input('memberToken'));
        if(!$token) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        $member = $this->memberService->verify($token->memberId, $request->input('verificationCode'));
        if($member == null) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_MSG);
        $dataJson = new MmeberResource($member);
        $this->logService = new ActionSucessLogService('MemberController', $member->id, 'verify');
        $this->logService->printLog();
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
