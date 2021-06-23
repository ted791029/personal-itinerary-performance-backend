<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use resources\views;

class ForgetPasswordVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;


    private $params;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $code)
    {
        $this->params['name'] =  $name;
        $this->params['code'] = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       // 透過 with 把參數指定給 view
       return $this->subject("Pip驗證碼-忘記密碼")
       ->view('emails.forgetPasswordVerificationCode')
       ->with([
           'params' => $this->params,
       ]);
    }
}
