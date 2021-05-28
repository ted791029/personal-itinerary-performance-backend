<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Validator\MemberValidator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MemberResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;

class MemberController extends Controller
{   
    private $memberService;
    private $memberValidator;

    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->memberValidator = new MemberValidator();
    }
        
    /**
     * 取得所有會員
     *
     * @return void
     */
    public function get()
    {
        $dataJson = MemberResource::collection($this->memberService->getList());
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }  
    /**
     * 依照id 取得會員
     *
     * @param  mixed $id
     * @return void
     */
    public function getById($id)
    {
        $dataJson = new MemberResource($this->memberService->getById($id));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    /**
     * 註冊
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request){
        $validate = $this->memberValidator->register($request);
        if($validate != null) return $validate;
        $dataJson = new MemberResource($this->memberService->register($request));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
    /**
     * 檢查是否註冊
     */
    public function isAccountExit($account){
        $member = $this->memberService->getByAccount($account);
        if($member == null ) return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        else  return ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
    }
}
