<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\MemberService;
use App\Services\MemberTokenService;

class AuthService
{
    private $membrService;
    private $memberTokenService;

    public function __construct()
    {
        $this->membrService = new MemberService();
        $this->memberTokenService = new MemberTokenService();
    }
    
    /**
     * 註冊會員
     */
    public function register(Request $request)
    {      
        $member = $this->membrService->store($request);
        return $this->memberTokenService->createToken($member->id);
    }

    /**
     * 登入
     */
    public function login(Request $request)
    {
        $member = $this->membrService->login($request->input('account'), $request->input('password'));
        return $member;
    }
}