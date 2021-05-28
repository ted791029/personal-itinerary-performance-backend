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
}