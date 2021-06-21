<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\MemberService;
use App\Services\MemberTokenService;

class AuthService
{
    private $memberService;
    private $memberTokenService;


    public function __construct(
        MemberService $memberService
    )
    {
        $this->memberService = $memberService;
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
}