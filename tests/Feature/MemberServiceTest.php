<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\VerificationCodeService;
use App\Mail\VerificationCodeMail;
use App\Services\MailService;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use App\Services\MemberService;
use App\Models\Member;
use App\Models\VerificationCode;
use App\Formatter\Constants;

use Tests\TestCase;

class MemberServiceTest extends TestCase
{
    //要注入的物件
    private $memberRepositoryMock;
    private $verificationCodeServiceMock;
    private $mailServiceMock;
    //service
    private $memberService;
    public function __construct()
    {
        parent :: __construct();
        $this->memberRepositoryMock = \Mockery::mock(MemberRepository::class);
        $this->verificationCodeServiceMock = \Mockery::mock(VerificationCodeService::class);
        $this->mailServiceMock = \Mockery::mock(MailService::class);
        $this->memberService = new MemberService($this->memberRepositoryMock, $this->verificationCodeServiceMock, $this->mailServiceMock);
    }   
    /**
     * 產生會員-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testStoreBySuccess()
    {
         /******建立需要的參數*******/
         $request = Request::create('api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account' => 'azoocx791029@gmail.com',
            'password' => 'd86843555',
        ));
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->memberRepositoryMock->shouldReceive('store')->andReturn($member);
        $this->assertEquals($this->memberService->store($request), $returnData);
    }
    /**
     * 依照id 取得會員-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testGetByIdBySuccess()
    {
         /******建立需要的參數*******/
        $id = 1;
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->getById($id), $returnData);
    }
    /**
     * 依照account 取得會員-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testGetByAccountBySuccess()
    {
         /******建立需要的參數*******/
        $account = 'azoocx791029@gmail.com';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->memberRepositoryMock->shouldReceive('filterByAccount');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->getByAccount($account), $returnData);
    }
    /**
     * 寄驗證信-查無會員
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendVerificationCodeByCreateVerificationCode()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn(null);
        $this->verificationCodeServiceMock->shouldReceive('createVerificationCode')->andReturn($verificationCode);
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn(null);
        $this->assertEquals($this->memberService->sendVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 寄驗證信-會員沒有信箱
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendVerificationCodeByMemberNotFindEmail()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = null;
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn($verificationCode);
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->sendVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 寄驗證信-會員沒有姓名
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendVerificationCodeByMemberNotFindName()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = null;
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn($verificationCode);
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->sendVerificationCode($memberId, $type), $returnData);
    }

    /**
     * 寄驗證信-沒有驗證碼
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendVerificationCodeByVerificationCodeNotFindCode()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = null;
        $verificationCode->status = null;
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn($verificationCode);
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->sendVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 寄驗證信-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendVerificationCodeBySucess()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '001';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = $verificationCode;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn($verificationCode);
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->mailServiceMock->shouldReceive('send');
        $this->assertEquals($this->memberService->sendVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 驗證-驗證碼錯誤
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendverifyByCodeError()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $code = '123456';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn(null);
        $this->assertEquals($this->memberService->verify($memberId, $code, $type), $returnData);
    }
    /**
     * 驗證-查無會員
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendverifyByNotFindMember()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '001';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn($verificationCode);
        $this->verificationCodeServiceMock->shouldReceive('update');
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn(null);
        $this->assertEquals($this->memberService->verify($memberId, $code, $type), $returnData);
    }
    /**
     * 驗證-會員已驗證
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendverifyByMemberIsVerified()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '001';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 1; //已驗證
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn($verificationCode);
        $this->verificationCodeServiceMock->shouldReceive('update');
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->assertEquals($this->memberService->verify($memberId, $code, $type), $returnData);
    }
    /**
     * 驗證-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendverifyBySucess()
    {
        /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $MEMBER_VERIFICATION_CODE;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '001';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn($verificationCode);
        $this->verificationCodeServiceMock->shouldReceive('update');
        $this->memberRepositoryMock->shouldReceive('filterById');
        $this->memberRepositoryMock->shouldReceive('get')->andReturn($member);
        $this->memberRepositoryMock->shouldReceive('upate');
        $this->assertEquals($this->memberService->verify($memberId, $code, $type), $returnData);
    }
}
