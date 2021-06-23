<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\MemberService;
use App\Services\MemberTokenService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberToken;
use App\Services\VerificationCodeService;
use App\Services\MailService;
use App\Formatter\Constants;
use App\Models\VerificationCode;

class AuthServiceTest extends TestCase
{
    //要注入的物件
    private $memberServiceMock;
    private $verificationCodeServiceMock;
    private $mailServiceMock;
    //service
    private $authService;
    public function __construct()
    {
        parent :: __construct();
        $this->memberServiceMock = \Mockery::mock(MemberService::class);
        $this->verificationCodeServiceMock = \Mockery::mock(VerificationCodeService::class);
        $this->mailServiceMock = \Mockery::mock(MailService::class);
        $this->authService = new AuthService($this->memberServiceMock, $this->verificationCodeServiceMock, $this->mailServiceMock);
    }
    public function testRegistert()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd86843555',
       ));
        $member = new Member();
        $member->id = '1';
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = 'd86843555';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = '0';;
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('store')->andReturn($member);
        $this->assertEquals($this->authService->register($request), $returnData);
    }

    public function testLogin()
    {
        $memberTokenServiceMock = \Mockery::mock(MemberTokenService::class);
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => 'd86843555',
       ));
        $member = new Member();
        $member->id = '1';
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = 'd86843555';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = '0';
        $returnData = $member;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('login')->andReturn($member);
        $this->assertEquals($this->authService->login($request), $returnData);
    }
    /**
     * 忘記密碼寄驗證信-查無會員
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByNotFindMember()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn(null);
        $this->assertEquals($this->authService->sendForgetPasswordVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 忘記密碼寄驗證信-會員姓名空值
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByNotFindMemberName()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $member = new Member();
        $member->id = 1;
        $member->name = null;
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn(null);
        $this->verificationCodeServiceMock->shouldReceive('createVerificationCode')->andReturn($verificationCode);
        $this->assertEquals($this->authService->sendForgetPasswordVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 忘記密碼寄驗證信-會員帳號空值
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByNotFindMemberAccount()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = null;
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn(null);
        $this->verificationCodeServiceMock->shouldReceive('createVerificationCode')->andReturn($verificationCode);
        $this->assertEquals($this->authService->sendForgetPasswordVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 忘記密碼寄驗證信-驗證碼空值
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByNotFindCode()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = null;
        $verificationCode->status = null;
        $verificationCode->type = '002';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn(null);
        $this->verificationCodeServiceMock->shouldReceive('createVerificationCode')->andReturn($verificationCode);
        $this->assertEquals($this->authService->sendForgetPasswordVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 忘記密碼寄驗證信-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeBySucess()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
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
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCode')->andReturn(null);
        $this->verificationCodeServiceMock->shouldReceive('createVerificationCode')->andReturn($verificationCode);
        $this->mailServiceMock->shouldReceive('send');
        $this->assertEquals($this->authService->sendForgetPasswordVerificationCode($memberId, $type), $returnData);
    }
    /**
     * 忘記密碼驗證碼是否存在-會員姓名空值
     *
     * @param  mixed $request
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitByNotFindMemebr()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $returnData = null;
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn(null);
        $this->assertEquals($this->authService->forgetPasswordVerificationCodeIsExit($memberId, $code, $type), $returnData);
    }
    /**
     * 忘記密碼驗證碼是否存在-會員姓名空值
     *
     * @param  mixed $request
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitByVerificationCodeError()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
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
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn(null);
        $this->assertEquals($this->authService->forgetPasswordVerificationCodeIsExit($memberId, $code, $type), $returnData);
    }
     /**
     * 忘記密碼驗證碼是否存在-成功
     *
     * @param  mixed $request
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitBySucess()
    {
         /******建立需要的參數*******/
        $memberId = 1;
        $code = '123456';
        $type = Constants :: $FORGET_PASSWORD_VERIFICATION_CODE;
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
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
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->verificationCodeServiceMock->shouldReceive('getVerificationCodeByCode')->andReturn($returnData);
        $this->assertEquals($this->authService->forgetPasswordVerificationCodeIsExit($memberId, $code, $type), $returnData);
    }
}
