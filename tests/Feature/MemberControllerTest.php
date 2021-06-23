<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\MemberService;
use App\Validator\MemberValidator;
use App\Services\MemberTokenService;
use App\Http\Resources\VerificationCodeResource;
use App\Http\Resources\MmeberResource;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use  App\Http\Controllers\MemberController;
use App\Models\MemberToken;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\VerificationCode;


class MemberControllerTest extends TestCase
{
    //要注入的物件
    private $memberServiceMock;
    private $memberValidatorMock;
    private $memberTokenServiceMock;
    //controller
    private $memberController;
    public function __construct()
    {
        parent :: __construct();
        $this->memberServiceMock = \Mockery::mock(MemberService::class);
        $this->memberValidatorMock = \Mockery::mock(MemberValidator::class);
        $this->memberTokenServiceMock = \Mockery::mock(MemberTokenService::class);
        $this->memberController = new MemberController($this->memberServiceMock, $this->memberTokenServiceMock, $this->memberValidatorMock);
    }   
     /**
     * 依照token 取得會員-無效token
     *
     * @param  mixed $id
     * @return void
     */
    public function testGetByTokenByErrorToken()
    {
        /******建立需要的參數*******/
        $memberToken = 'failToken';
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn(null);
        $this->assertEquals($this->memberController->getByToken($memberToken), $returnData);
    }
    /**
     * 依照token 取得會員-成功
     *
     * @param  mixed $id
     * @return void
     */
    public function testGetByTokenByTokenSucess()
    {
        /******建立需要的參數*******/
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $token = '11b5c5f0000000005499ec42908352bd';
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $dataJson = new MmeberResource($member);
        $returnData = ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        /******設定方法及回傳參數*******/
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn($memberToken);
        $this->memberServiceMock->shouldReceive('getById')->andReturn($member);
        $this->assertEquals($this->memberController->getByToken($token), $returnData);
    }        
    /**
     * 寄驗證信-檢驗失敗
     *
     * @return void
     */
    public function testSendVerificationCodeByFailedVerification()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd'
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('sendVerificationCode')->andReturn($returnData);
        $this->assertEquals($this->memberController->sendVerificationCode($request), $returnData);
    }
    /**
     * 寄驗證信-無效toekn
     *
     * @return void
     */
    public function testSendVerificationCodeByErrorToken()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd'
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('sendVerificationCode')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn(null);
        $this->assertEquals($this->memberController->sendVerificationCode($request), $returnData);
    }
    /**
     * 寄驗證信-無法寄出
     *
     * @return void
     */
    public function testSendVerificationCodeByFailedSend()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd'
        ));
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_CODE, ResponseCodeInfo::$RESPONSE_NOT_FAILED_SEND_VERIFICATION_CODE_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('sendVerificationCode')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn($memberToken);
        $this->memberServiceMock->shouldReceive('sendVerificationCode')->andReturn(null);
        $this->assertEquals($this->memberController->sendVerificationCode($request), $returnData);
    }
    /**
     * 寄驗證信-成功
     *
     * @return void
     */
    public function testSendVerificationCodeBySucess()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd'
        ));
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $verificationCode = new VerificationCode();
        $verificationCode->id = 1;
        $verificationCode->memberId = 1;
        $verificationCode->code = '123456';
        $verificationCode->status = null;
        $verificationCode->type = '001';
        $verificationCode->created_at = '2021-05-31 22:49:20';
        $verificationCode->updated_at = '2021-05-31 22:49:20';
        $dataJson = new VerificationCodeResource($verificationCode);
        $returnData = ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('sendVerificationCode')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn($memberToken);
        $this->memberServiceMock->shouldReceive('sendVerificationCode')->andReturn($verificationCode);
        $this->assertEquals($this->memberController->sendVerificationCode($request), $returnData);
    }
    /**
     * 驗證-驗證失敗
     *
     * @return void
     */
    public function testVerifyByFailedVerification()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => '123456',
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('verify')->andReturn($returnData);
        $this->assertEquals($this->memberController->verify($request), $returnData);
    }
    /**
     * 驗證-無效token
     *
     * @return void
     */
    public function testVerifyByErrorToken()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => '123456',
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('verify')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn(null);
        $this->assertEquals($this->memberController->verify($request), $returnData);
    }
    /**
     * 驗證-查無會員
     *
     * @return void
     */
    public function testVerifyByMemberNotFind()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => '123456',
        ));
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_VERIFY_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('verify')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn($memberToken);
        $this->memberServiceMock->shouldReceive('verify')->andReturn(null);
        $this->assertEquals($this->memberController->verify($request), $returnData);
    }
    /**
     * 驗證-驗證成功
     *
     * @return void
     */
    public function testVerifyBySucess()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => '123456',
        ));
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $dataJson = new MmeberResource($member);
        $returnData = ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        /******設定方法及回傳參數*******/
        $this->memberValidatorMock->shouldReceive('verify')->andReturn(null);
        $this->memberTokenServiceMock->shouldReceive('getToken')->andReturn($memberToken);
        $this->memberServiceMock->shouldReceive('verify')->andReturn($member);
        $this->assertEquals($this->memberController->verify($request), $returnData);
    }
}
