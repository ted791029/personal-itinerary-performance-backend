<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MmeberResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Services\MemberService;
use App\Validator\MemberValidator;

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
        $dataJson = MmeberResource::collection($this->memberService->getList());
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
        $dataJson = new MmeberResource($this->memberService->getById($id));
        return ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
    }
}
