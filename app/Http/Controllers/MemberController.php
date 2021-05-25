<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Validator\MemberValidator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{   
    private $memberService;
    private $memberValidator;

    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->memberValidator = new MemberValidator();
    }

    public function register(Request $request){
        $respon = $this->memberValidator->register($request);
        if($respon != null) return $respon;
        return $this->memberService->register($request);
    }
    
    public function get()
    {
        return $this->memberService->get();
    }

    public function getById($id)
    {
        return $this->memberService->getById($id);
    }
}
