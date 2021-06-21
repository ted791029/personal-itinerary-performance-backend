<?php

namespace App\Services\Log;

use Illuminate\Support\Facades\Log;

/**
 * 印出執行動作成功log
 */
class NullParmaLogService implements LogService   
{
    //類別名稱
    private $className;
    //會員ID
    private $memberId;
    //動作
    private $action;

    public function __construct(
        $className,
        $memberId,
        $mothed,
        $parma
    )
    {
        $this->className = $className;
        $this->memberId = $memberId;
        $this->mothed = $mothed;
        $this->parma = $parma;
    }

    public function printLog(){
        $message = $this->className . '-會員ID: [ ' . $this->memberId . ' ] ，執行方法: [ ' . $this->mothed . ' ] ， 屬性: [ ' . $this->parma .' ] 為空值' ; 
        Log::error($message);
    }
}