<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\Log;

class MemberService
{
    private $memberRepository;

    public function __construct()
    {
        $this->memberRepository = new MemberRepository();
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
     * 註冊會員
     */
    public function register(Request $request){
        
        return $this->memberRepository->store($request);
    }
}