<?php

namespace App\Services\Log;

use Illuminate\Support\Facades\Log;

/**
 * 印出執行動作成功log
 */
class ActionSucessLogService implements LogService   
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
        $action
    )
    {
        $this->className = $className;
        $this->memberId = $memberId;
        $this->action = $action;
    }

    public function printLog(){
        $message = $this->className . '-會員ID: [ ' . $this->memberId . ' ] ，執行動作: [ ' . $this->action . ' ] 成功'; 
        Log::info($message);
    }
}