<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\MailService;
use App\Services\MemberService;
use App\Services\MemberTokenService;
use App\Services\VerificationCodeService;
use App\Mail\VerificationCodeMail;
use App\Util\IdUtil;

class AuthService
{
    private $verificationCodeService;
    private $membrService;
    private $memberTokenService;
    private $mailService;

    public function __construct()
    {
        $this->mailService = new MailService();
        $this->membrService = new MemberService();
        $this->memberTokenService = new MemberTokenService();
        $this->verificationCodeService = new VerificationCodeService();
    }
    
    /**
     * 註冊會員
     */
    public function register(Request $request)
    {
        
        $member = $this->membrService->store($request);
        $unixtime = strtotime("+1 week");
        $expiryTime = Carbon::createFromTimeStamp($unixtime);
        $memberToken['token'] = IdUtil::getId32();
        $memberToken['memberId'] = $member->id;
        $memberToken['expiryTime'] = $expiryTime;
        return $this->memberTokenService->store($memberToken);
    }
    
    /**
     * 寄出驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode($token){
        $memberId = $token->memberId;
        $verificationCode = $this->verificationCodeService->getVerificationCode($memberId);
        if($verificationCode == null) $verificationCode =  $this->verificationCodeService->createVerificationCode($memberId);
        $member = $this->membrService->getById($memberId);
        if($member == null) return;
        $email = $member->account;
        $name = $member->name;
        if($email == null || $name == null) return;
        $code = $verificationCode->code;
        if($code == null) return;
        $this->mailService->send($email, new VerificationCodeMail($name, $code));
        return $verificationCode;
    }
}