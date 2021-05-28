<?php

namespace App\Repositories;

//use Your Model
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Class MemberRepository.
 */
class MemberRepository
{
    private $member;
    private $db;

    public function __construct()
    {
        $this->member = new Member();
        $this->db = DB::table('members');
    }

    public function store(Request $request)
    {
        $member = $this->member::create($request->all());
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
}
