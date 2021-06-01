<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//use Your Model
use App\Models\VerificationCode;
/**
 * Class VerificationCodeRepository.
 */
class VerificationCodeRepository
{
    private $verificationCode;
    private $db;

    public function __construct()
    {
        $this->verificationCode = new VerificationCode();
        $this->db = DB::table('verification_codes');
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
     * store
     *
     * @param  mixed $verificationCodeInput
     * @return void
     */
    public function store($inputs)
    {
        $verificationCode = $this->verificationCode::create($inputs);
        return $verificationCode;
    }    
    /**
     * update
     *
     * @param  mixed $verificationCode
     * @return void
     */
    public function update($verificationCode){
        $unixtime = time();
        $now = Carbon::createFromTimeStamp($unixtime);
        $arr = [
            'status' => $verificationCode->status,
            'updated_at' => $now
        ]; 
        return $this->db->where('id', $verificationCode->id)->update($arr);
    }
   /**
     *增加 memebriId條件
     */
    public function filterByMemberId($memberId)
    {   
        $this->db->where('memberId', $memberId);
    }

    /**
     *增加 創造時間條件
     */
    public function filterByCreated($time, $comparator)
    {   
        $this->db->where('created_at', $comparator, $time);
    }

    /**
     *增加 狀態條件
     */
    public function filterByStatus($status)
    {   
        $this->db->where('status', $status);
    }
    /**
     *增加 驗證碼條件
     */
    public function filterByCode($code)
    {   
        $this->db->where('code', $code);
    }
}
