<?php

namespace App\Validator;

use Illuminate\Http\Request;
use App\Services\MemberService;

class MemberValidator
{
    private $memberService;

    public function __construct()
    {
        $this->memberService = new MemberService();
    }

    /**
     * 註冊驗證
     */
    public function register(Request $request){
        $account = $request->input('account');
        $password = $request->input('password');

        if($account == null) return;
        if($password == null) return;
        $reg = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/";
        if(!(preg_match( $reg, $password))) return response('密碼不符合格式', 200);
        $member = $this->memberService->getByAccount($account);
        if($member != null) return response('此帳號已經註冊', 200);
    }
}