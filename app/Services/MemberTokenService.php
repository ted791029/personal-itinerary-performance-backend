<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\MemberTokenRepository;
use Carbon\Carbon;

class MemberTokenService
{
    private $memberTokenRepository;

    public function __construct()
    {
        $this->memberTokenRepository = new MemberTokenRepository();
    }

    /**
     * @return string
     *  Return the model
     */
    public function store($inputs)
    {
        return $this->memberTokenRepository->store($inputs);
    }
    
    /**
     * 取得token
     *
     * @param  mixed $request
     * @return void
     */
    public function getToken($memberToken){
        $this->memberTokenRepository->filterByToken($memberToken);
        $unixtime = time();
        $now = Carbon::createFromTimeStamp($unixtime);
        $this->memberTokenRepository->filterByExpiryTime($now);
        return  $this->memberTokenRepository->get();
    }
}
?>