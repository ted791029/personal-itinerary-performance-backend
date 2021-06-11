<?php

namespace App\Validator;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Validator\Validator;

class MemberValidator extends Validator
{
    private $memberService;

    public function __construct(
        MemberService $memberService
    )
    {
        $this->memberService = $memberService;
    }

    /**
     * 寄出驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCode(Request $request){
        $keys = ['memberToken'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
    }    
    /**
     * 驗證
     *
     * @param  mixed $request
     * @return void
     */
    public function verify(Request $request){
        $keys = ['memberToken', 'verificationCode'];
        $inputValidate = parent::validateInputs($request, $keys);
        if($inputValidate != null) return $inputValidate;
    }
}