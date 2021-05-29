<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\Log;
use App\Repositories\VerificationCodeRepository;
use Carbon\Carbon;
use App\Services\MailService;
use App\Services\MemberService;
use App\Mail\VerificationCodeMail;

class AuthService
{
    private $memberRepository;
    private $verificationCodeRepository;
    private $verificationCodeService;
    private $membrService;

    public function __construct()
    {
        $this->memberRepository = new MemberRepository();
        $this->verificationCodeRepository = new VerificationCodeRepository();
        $this->mailService = new MailService();
        $this->membrService = new MemberService();
    }
    
    /**
     * 註冊會員
     */
    public function register(Request $request)
    {
        
        return $this->memberRepository->store($request);
    }
    
    /**
     * 寄出驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode(Request $request){

        $verificationCode = $this->getVerificationCode($request);
        if($verificationCode == null) $verificationCode =  $this->createVerificationCode($request);
        $member = $this->membrService->getById($request->input('memberId'));
        if($member == null) return;
        $email = $member->account;
        $name = $member->name;
        if($email == null || $name == null) return;
        $code = $verificationCode->code;
        if($code == null) return;
        $this->mailService->send($email, new VerificationCodeMail($name, $code));
        return $verificationCode;
    }
    /**
     * 創造驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function createVerificationCode(Request $request)
    {
        $memberId = $request->input('memberId');
        $codeSize = 6;
        $code = $this->randomkeys($codeSize);
        $verificationCodeInput['memberId'] = $memberId;
        $verificationCodeInput['code'] = $code;
        return $this->verificationCodeRepository->store($verificationCodeInput);
    }
        
    /**
     * 取得驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function getVerificationCode(Request $request)
    {
        $memberId = $request->input('memberId');
        $unixtime = strtotime("- 10 minutes");
        $time = Carbon::createFromTimeStamp($unixtime);
        $this->verificationCodeRepository->filterByMemberId($memberId);
        $this->verificationCodeRepository->createdAfterTime($time);
        return $this->verificationCodeRepository->get();
    }
    
    /**
     * 產生亂數
     *
     * @param  mixed $length
     * @return void
     */
    private function randomkeys($length)   
    {   
        $output='';   
        for ($a = 0; $a<$length; $a++) {   
            $output .= mt_rand(0, 9);    //生成php隨機數   
        }   
        return $output; 
    }  
}