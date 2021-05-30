<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\VerificationCodeRepository;
use Carbon\Carbon;
use App\Formatter\Constants;

class VerificationCodeService
{
    private $verificationCodeRepository;

    public function __construct()
    {
        $this->verificationCodeRepository = new VerificationCodeRepository();
    }
    
    /**
     * @return string
     *  Return the model
     */
    public function get()
    {
        return $this->db->get()->first();
    }      
    /**
     * 取得驗證碼
     * @param  mixed $request
     * @return void
     */
    public function getVerificationCode($memberId)
    {
        $unixtime = strtotime("- 10 minutes");
        $time = Carbon::createFromTimeStamp($unixtime);
        $this->verificationCodeRepository->filterByMemberId($memberId);
        $this->verificationCodeRepository->createdAfterTime($time);
        $this->verificationCodeRepository->filterByStatus(Constants::$STATUS_DISABLE);
        return $this->verificationCodeRepository->get();
    }
    /**
     * 創造驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function createVerificationCode($memberId)
    {
        $codeSize = 6;
        $code = $this->randomkeys($codeSize);
        $verificationCodeInput['memberId'] = $memberId;
        $verificationCodeInput['code'] = $code;
        return $this->verificationCodeRepository->store($verificationCodeInput);
    }
    /**
     * 產生亂數
     *
     * @param  mixed $length
     * @return void
     */
    private function randomkeys($length)   
    {   
        $output='';   
        for ($a = 0; $a<$length; $a++) {   
            $output .= mt_rand(0, 9);    //生成php隨機數   
        }   
        return $output; 
    }  
}
?>