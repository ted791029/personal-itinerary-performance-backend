<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\MemberTokenService;
use App\Repositories\MemberTokenRepository;
use App\Models\MemberToken;

class MemberTokenServiceTest extends TestCase
{
    private $memberTokenRepositoryMock;

    //service
    private $memberTokenService;
    public function __construct()
    {
        parent :: __construct();
        $this->memberTokenRepositoryMock = \Mockery::mock(MemberTokenRepository::class);
        $this->memberTokenService = new MemberTokenService($this->memberTokenRepositoryMock);
    }

    /**
     *token寫入資料庫 
     */
    public function testStore()
    {
        /******建立需要的參數*******/
        $inputs['token'] = '11b5c5f0000000005499ec42908352bd';
        $inputs['memberId'] = 1;
        $inputs['expiryTime'] = "2021-06-23 00:00:00";
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = $memberToken;
        /******設定方法及回傳參數*******/
        $this->memberTokenRepositoryMock->shouldReceive('store')->andReturn($returnData);
        $this->assertEquals($this->memberTokenService->store($inputs), $returnData);
    }
    /**
     * 創建token
     */
    public function testCreateToken()
    {
        /******建立需要的參數*******/
        $inputs['token'] = '11b5c5f0000000005499ec42908352bd';
        $inputs['memberId'] = 1;
        $inputs['expiryTime'] = "2021-06-23 00:00:00";
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = $memberToken;
        /******設定方法及回傳參數*******/
        $this->memberTokenRepositoryMock->shouldReceive('store')->andReturn($returnData);
        $this->assertEquals($this->memberTokenService->createToken($inputs), $returnData);
    }
    /**
     * 取得token
     */
    public function testGetToken()
    {
        /******建立需要的參數*******/
        $token = '11b5c5f0000000005499ec42908352bd';
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = $memberToken;
        /******設定方法及回傳參數*******/
        $this->memberTokenRepositoryMock->shouldReceive('filterByToken');
        $this->memberTokenRepositoryMock->shouldReceive('filterByExpiryTime');
        $this->memberTokenRepositoryMock->shouldReceive('get')->andReturn($returnData);
        $this->assertEquals($this->memberTokenService->getToken($token), $returnData);
    }
    /**
     * 取得token
     */
    public function testGetTokenByMemberId()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = $memberToken;
        /******設定方法及回傳參數*******/
        $this->memberTokenRepositoryMock->shouldReceive('filterByMemberId');
        $this->memberTokenRepositoryMock->shouldReceive('filterByExpiryTime');
        $this->memberTokenRepositoryMock->shouldReceive('get')->andReturn($returnData);
        $this->assertEquals($this->memberTokenService->getTokenByMemberId($memberId), $returnData);
    }
}
