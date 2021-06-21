<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\Log;
use App\Services\VerificationCodeService;
use App\Mail\VerificationCodeMail;
use App\Services\MailService;
use Illuminate\Support\Facades\Hash;
use App\Formatter\Constants;

class MemberService
{
    private $memberRepository;
    private $verificationCodeService;
    private $mailService;

    public function __construct(
        MemberRepository $memberRepository,
        VerificationCodeService $verificationCodeService,
        MailService $mailService
    )
    {
        $this->memberRepository = $memberRepository;
        $this->verificationCodeService = $verificationCodeService;
        $this->mailService = $mailService;
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
    public function sendVerificationCode($memberId){
        $verificationCode = $this->verificationCodeService->getVerificationCode($memberId);
        if(!$verificationCode) $verificationCode =  $this->verificationCodeService->createVerificationCode($memberId);
        $member = $this->getById($memberId);
        if(!$member) return;
        $email = $member->account;
        $name = $member->name;
        if(!$email|| !$name) return;
        $code = $verificationCode->code;
        if(!$code) return;
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
        if(!$member) return;
        //檢查資料庫與目前密碼加密後是否相同
        $booleanValue = Hash::check($password,$member->password);
        if($booleanValue) return $member;
        else return;
    } 
    /**
     * 驗證
     *
     * @param  mixed $memberId
     * @param  mixed $verificationCode
     * @return void
     */
    public function verify($memberId, $code){
        $verificationCode = $this->verificationCodeService->getVerificationCodeByCode($memberId, $code);
        if(!$verificationCode)  return;
        $verificationCode->status = Constants :: $STATUS_ENABLE;
        $this->verificationCodeService->update($verificationCode);
        $member = $this->getById($memberId);
        if(!$member)  return;
        if($member->verifyStatus == Constants :: $STATUS_ENABLE) return;
        $member->verifyStatus = Constants :: $STATUS_ENABLE;
        $this->memberRepository->upate($member);
        return $member;
    }
}
?>