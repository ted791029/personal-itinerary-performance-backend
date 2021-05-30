<?php

namespace App\Repositories;

//use Your Model
use App\Models\MemberToken;
use Illuminate\Support\Facades\DB;

/**
 * Class MemberTokenRepository.
 */
class MemberTokenRepository
{

    private $memberToken;
    private $db;

    public function __construct()
    {
        $this->memberToken = new MemberToken();
        $this->db = DB::table('member_tokens');
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
     * @return string
     *  Return the model
     */
    public function store($inputs)
    {
        $memberToken = $this->memberToken::create($inputs);
        return $memberToken;
    }
    
    /**
     * 依照token篩選
     *
     * @param  mixed $memberId
     * @return void
     */
    public function filterByToken($token){
        $this->db->where('token', $token);
    }

    /**
     * 依照有效期篩選
     *
     * @param  mixed $memberId
     * @return void
     */
    public function filterByExpiryTime($time){
        $this->db->where('expiryTime', '>' , $time);
    }
}
