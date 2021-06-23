<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\MemberService;
use App\Services\MemberTokenService;
use App\Services\VerificationCodeService;
use App\Services\MailService;
use App\Mail\ForgetPasswordVerificationCodeMail;

class AuthService
{
    private $memberService;
    private $memberTokenService;
    private $verificationCodeService;
    private $mailService;


    public function __construct(
        MemberService $memberService,
        VerificationCodeService $verificationCodeService,
        MailService $mailService
    )
    {
        $this->memberService = $memberService;
        $this->verificationCodeService = $verificationCodeService;
        $this->mailService = $mailService;
    }
    
    /**
     * 註冊會員
     */
    public function register(Request $request)
    {      
        return $this->memberService->store($request);
    }
    /**
     * 登入
     */
    public function login(Request $request)
    {
        $member = $this->memberService->login($request->input('account'), $request->input('password'));
        return $member;
    }
    /**
     * 檢查是否註冊
     */
    public function isAccountExit($account)
    {
        return $this->memberService->getByAccount($account);
        
    }   
    /**
     * 忘記密碼寄驗證信
     *
     * @param  mixed $account
     * @param  mixed $type
     * @return void
     */
    public function sendForgetPasswordVerificationCode($account, $type){
        $member = $this->memberService->getByAccount($account);
        if(!$member) return;
        $memberId = $member->id;
        if(!$memberId) return;
        $verificationCode = $this->verificationCodeService->getVerificationCode($memberId, $type);
        if(!$verificationCode) $verificationCode =  $this->verificationCodeService->createVerificationCode($memberId, $type);
        $email = $member->account;
        $name = $member->name;
        if(!$email || !$name) return;
        $code = $verificationCode->code;
        if(!$code) return;
        $this->mailService->send($email, new ForgetPasswordVerificationCodeMail($name, $code));
        return $verificationCode;
    }    
    /**
     * 忘記密碼驗證碼是否存在
     *
     * @param  mixed $account
     * @param  mixed $code
     * @param  mixed $type
     * @return void
     */
    public function forgetPasswordVerificationCodeIsExit($account, $code, $type){
        $member = $this->memberService->getByAccount($account);
        if(!$member) return;
        $memberId = $member->id;
        $verificationCode = $this->verificationCodeService->getVerificationCodeByCode($memberId, $code, $type);
        if(!$verificationCode)  return;
        return $verificationCode;
    }
}