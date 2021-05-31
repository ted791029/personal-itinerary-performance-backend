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

class MemberController extends Controller
{
    private $memberService;
    private $memberTokenService;
    private $memberValidator;

    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->memberTokenService = new MemberTokenService();
        $this->memberValidator = new MemberValidator();
    }
    
    /**
     * 依照id 取得會員
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
        $dataJson = new VerificationCodeResource($this->memberService->sendVerificationCode($token));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
