<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

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
     *增加 memebriId條件
     */
    public function filterByMemberId($memberId)
    {   
        $this->db->where('memberId', $memberId);
    }

    /**
     *增加 創造時間 > 輸入時間
     */
    public function createdAfterTime($time)
    {   
        $this->db->where('created_at', '>', $time);
    }

     /**
     *增加 狀態條件
     */
    public function filterByStatus($status)
    {   
        $this->db->where('status', $status);
    }

}
