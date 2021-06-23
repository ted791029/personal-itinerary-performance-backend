<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\VerificationCodeService;
use App\Repositories\VerificationCodeRepository;
use App\Models\VerificationCode;

class VerificationCodeServiceTest extends TestCase
{
    private $verificationCodeRepositoryMock;

    //service
    private $verificationCodeService;
    public function __construct()
    {
        parent :: __construct();
        $this->verificationCodeRepositoryMock = \Mockery::mock(VerificationCodeRepository::class);
        $this->verificationCodeService = new VerificationCodeService($this->verificationCodeRepositoryMock);
    }

    /**
     *取得驗證碼 
     */
    public function testGetVerificationCode()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $type = '001';
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = $verificationCode;
        $returnData = $verificationCode;
        /******設定方法及回傳參數*******/
        $this->verificationCodeRepositoryMock->shouldReceive('filterByMemberId');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByCreated');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByStatus');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByType');
        $this->verificationCodeRepositoryMock->shouldReceive('get')->andReturn($verificationCode);
        $this->assertEquals($this->verificationCodeService->getVerificationCode($memberId, $type), $returnData);
    }
    /**
     *依據code取得驗證碼 
     */
    public function testGetVerificationCodeByCode()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $type = '001';
        $code = '123456';
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = $verificationCode;
        /******設定方法及回傳參數*******/
        $this->verificationCodeRepositoryMock->shouldReceive('filterByMemberId');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByCode');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByCreated');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByStatus');
        $this->verificationCodeRepositoryMock->shouldReceive('filterByType');
        $this->verificationCodeRepositoryMock->shouldReceive('get')->andReturn($verificationCode);
        $this->assertEquals($this->verificationCodeService->getVerificationCodeByCode($memberId, $type, $code), $returnData);
    }
     /**
     *建立驗證碼 
     */
    public function testCreateVerificationCode()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $type = '001';
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = $verificationCode;
        /******設定方法及回傳參數*******/
        $this->verificationCodeRepositoryMock->shouldReceive('store')->andReturn($verificationCode);
        $this->assertEquals($this->verificationCodeService->createVerificationCode($memberId, $type), $returnData);
    }
    /**
     *更新驗證碼 
     */
    public function testUpdate()
    {
        /******建立需要的參數*******/
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = 1;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = $verificationCode;
        /******設定方法及回傳參數*******/
        $this->verificationCodeRepositoryMock->shouldReceive('update')->andReturn($returnData);
        $this->assertEquals($this->verificationCodeService->update($verificationCode), $returnData);
    }
}
