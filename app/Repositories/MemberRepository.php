<?php

namespace App\Repositories;

//use Your Model
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class MemberRepository.
 */
class MemberRepository
{
    private $member;
    private $db;

    public function __construct(
        Member $member
    )
    {
        $this->member = $member;
        $this->db = DB::table('members');
    }
    
    /**
     * store
     *
     * @param  mixed $inputs
     * @return void
     */
    public function store($inputs)
    {
        $member = $this->member::create($inputs);
        return $member;
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
    public function getList()
    {
        return $this->db->get();
    }    
    /**
     * upate
     *
     * @param  mixed $inputs
     * @return void
     */
    public function upate($member){
        $unixtime = time();
        $now = Carbon::createFromTimeStamp($unixtime);
        $arr = [
            'name' => $member->name,
            'updated_at' => $now,
            'verifyStatus' => $member->verifyStatus
        ]; 
        return $this->db->where('id', $member->id)->update($arr);
    }
    /**
     *增加 id條件
     */
    public function filterById($id)
    {   
        $this->db->where('id', $id);
    }

    /**
     * 增加 account條件 
     */
    public function filterByAccount($account)
    {   
        $this->db->where('account', $account);
    }

    /**
     *增加 密碼條件
     */
    public function filterByPassword($password)
    {   
        $this->db->where('password', $password);
    }
}
