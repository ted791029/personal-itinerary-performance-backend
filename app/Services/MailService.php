<?php

namespace App\Services;
use Illuminate\Http\Request;
// 記得使用 use
use Illuminate\Support\Facades\Mail;


class MailService
{
    public function send($email, $mailObject)
    {
        // 收件者務必使用 collect 指定二維陣列，每個項目務必包含 "name", "email"
        $to = collect([
            ['email' => $email]
        ]);
 
        // 若要直接檢視模板
        // echo (new Warning($data))->render();die;
 
        Mail::to($to)->send($mailObject);
    }
}