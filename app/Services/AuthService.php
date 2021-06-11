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
        MemberService $memberService, 
        MemberTokenService $memberTokenService
    )
    {
        $this->memberService = $memberService;
        $this->memberTokenService = $memberTokenService;
    }
    
    /**
     * 註冊會員
     */
    public function register(Request $request)
    {      
        $member = $this->memberService->store($request);
        return $this->memberTokenService->createToken($member->id);
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