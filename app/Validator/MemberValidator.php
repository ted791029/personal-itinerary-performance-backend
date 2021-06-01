<?php

namespace App\Validator;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Validator\Validator;

class MemberValidator
{
    private $memberService;

    public function __construct()
    {
        $this->memberService = new MemberService();
    }

    /**
     * 產生驗證碼驗證
     */
    public function sendVerificationCode(Request $request){
        $keys = ['memberToken'];
        $inputValidate = Validator::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
    }

    public function verify(Request $request){
        $keys = ['memberToken', 'verificationCode'];
        $inputValidate = Validator::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
    }
}