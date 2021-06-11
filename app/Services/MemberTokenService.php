<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Repositories\MemberTokenRepository;
use Carbon\Carbon;
use App\Util\IdUtil;

class MemberTokenService
{
    private $memberTokenRepository;

    public function __construct(
        MemberTokenRepository $memberTokenRepository
    )
    {
        $this->memberTokenRepository = $memberTokenRepository;
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
     * 產生token
     *
     * @param  mixed $memberId
     * @return void
     */
    public function createToken($memberId){
        $unixtime = strtotime("+1 week");
        $expiryTime = Carbon::createFromTimeStamp($unixtime);
        $memberToken['token'] = IdUtil::getId32();
        $memberToken['memberId'] = $memberId;
        $memberToken['expiryTime'] = $expiryTime;
        return $this->store($memberToken);
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
        $this->memberTokenRepository->filterByExpiryTime($now, '>');
        return  $this->memberTokenRepository->get();
    }

    /**
     * 取得token
     *
     * @param  mixed $request
     * @return void
     */
    public function getTokenByMemberId($memberId){
        $this->memberTokenRepository->filterByMemberId($memberId);
        $unixtime = time();
        $now = Carbon::createFromTimeStamp($unixtime);
        $this->memberTokenRepository->filterByExpiryTime($now, '>');
        return  $this->memberTokenRepository->get();
    }
}
?>