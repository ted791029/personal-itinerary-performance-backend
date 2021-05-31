<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\Log;
use App\Services\VerificationCodeService;
use App\Mail\VerificationCodeMail;
use App\Services\MailService;
use Illuminate\Support\Facades\Hash;

class MemberService
{
    private $memberRepository;
    private $verificationCodeService;
    private $mailService;

    public function __construct()
    {
        $this->memberRepository = new MemberRepository();
        $this->verificationCodeService = new VerificationCodeService();
        $this->mailService = new MailService();
    }
    
    /**
     * 產生會員
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $inputs = $request->input();
        $hashPassword = Hash::make($inputs['password']);
        $inputs['password'] = $hashPassword ;
        return $this->memberRepository->store($inputs); 
    }
    /**
     * 取得所有會員
     */
    public function getList()
    {
        $member = $this->memberRepository->getList(); 
        return $member;
    }
    /**
     * 依照id 取得會員
     */
    public function getById($id)
    {
        $this->memberRepository->filterById($id);
        $member = $this->memberRepository->get(); 
        return $member;
    }
    /**
     * 依照account 取得會員
     */
    public function getByAccount($account)
    {
        $this->memberRepository->filterByAccount($account);
        $member = $this->memberRepository->get(); 
        return $member;
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
        $member = $this->getById($memberId);
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
     * 登入
     *
     * @param  mixed $request
     * @return void
     */
    public function login($account, $password){   
        $this->memberRepository->filterByAccount($account);
        $member =$this->memberRepository->get();
        //檢查資料庫與目前密碼加密後是否相同
        $booleanValue = Hash::check($password,$member->password);
        if($booleanValue) return $member;
        else return null;
    }
}
?>