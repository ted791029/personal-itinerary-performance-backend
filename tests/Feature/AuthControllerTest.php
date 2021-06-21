<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\AuthController;
use App\Services\AuthService;
use App\Services\MemberTokenService;
use App\Validator\AuthValidator;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\AuthResource;
use App\Models\MemberToken;

class AuthControllerTest extends TestCase
{

    //要注入的物件
    private $authServiceMock;
    private $authValidatorMock;
    private $memberTokenServiceMock;
    //controller
    private $authController;
    public function __construct()
    {
        parent :: __construct();
        $this->authServiceMock = \Mockery::mock(AuthService::class);
        $this->authValidatorMock = \Mockery::mock(AuthValidator::class);
        $this->memberTokenServiceMock = \Mockery::mock(MemberTokenService::class);
        $this->authController = new AuthController($this->authServiceMock, $this->authValidatorMock, $this->memberTokenServiceMock);
    }
    /**
     * 帳號是否存在-存在
     *
     * @return void
     */
    public function testIsAccountExitByExit()
    {
        $account = 'azoocx791029@gmail.com';
        /******建立需要的參數*******/
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->authServiceMock->shouldReceive('isAccountExit')->andReturn($member);
        $this->assertEquals($this->authController->isAccountExit($account), $returnData);
    }
    
    /**
     * 帳號是否存在-不存在
     *
     * @return void
     */
    public function testIsAccountExitByNotExit()
    {
        
        /******建立需要的參數*******/
        $account = 'azoocx7910291@gmail.com';
        $member = null;
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        /******設定方法及回傳參數*******/
        $this->authServiceMock->shouldReceive('isAccountExit')->andReturn($member);
        $this->assertEquals($this->authController->isAccountExit($account), $returnData);
    }

    /**
     * 註冊-驗證未過
     *
     * @return void
     */
    public function testRegisterByFailedVerification()
    {
        /******建立需要的參數*******/
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
        $request = Request::create('/api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('register')->andReturn($returnData);
        $this->assertEquals($this->authController->register($request), $returnData);
    }    
    /**
     * 註冊-寫入會員資料失敗
     *
     * @return void
     */
    public function testRegisterByInsertMemberError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('register')->andReturn(null);
        $this->authServiceMock->shouldReceive('register')->andReturn(null);
        $this->assertEquals($this->authController->register($request), $returnData);
    }
    /**
     * 註冊-寫入Token資料失敗
     *
     * @return void
     */
    public function testRegisterByInsertTokenError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_TOKEN_ERROR_CODE, ResponseCodeInfo::$RESPONSE_TOKEN__ERROR_MSG);
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('register')->andReturn(null);
        $this->authServiceMock->shouldReceive('register')->andReturn($member);
        $this->memberTokenServiceMock->shouldReceive('createToken')->andReturn(null);
        $this->assertEquals($this->authController->register($request), $returnData);
    }
    /**
     * 註冊-成功
     *
     * @return void
     */
    public function testRegisterBySucess()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/register', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $dataJson = new AuthResource($memberToken);
        $returnData = ResponseFormatter::jsonFormate($dataJson, ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('register')->andReturn(null);
        $this->authServiceMock->shouldReceive('register')->andReturn($member);
        $this->memberTokenServiceMock->shouldReceive('createToken')->andReturn($memberToken);
        $this->assertEquals($this->authController->register($request), $returnData);
    }

    /**
     * 登入-驗證未過
     *
     * @return void
     */
    public function testLoginByFailedVerification()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => 'd12345678'
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('login')->andReturn($returnData);
        $this->assertEquals($this->authController->login($request), $returnData);
    }

    /**
     * 登入-找不到
     *
     * @return void
     */
    public function testLoginByNotFind()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => 'd12345678'
       ));
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FOUND_ERROR_MSG);
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('login');
        $this->authServiceMock->shouldReceive('login')->andReturn(null);
        $this->assertEquals($this->authController->login($request), $returnData);
    }

    /**
     * 登入-成功
     *
     * @return void
     */
    public function testLoginBySucces()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => 'd12345678'
       ));
        $member = new Member();
        $member->id = 1;
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = '$10$jK7.IXXPmQb.zWTlNrpT9.ApCOGapQ3/FdAemm7fqtFE7I8gYrz6y';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = 0;
        $memberToken = new MemberToken();
        $memberToken->token = '11b5c5f0000000005499ec42908352bd';
        $memberToken->memberId = 1;
        $memberToken->expiryTime = '2021-06-10 22:49:20';
        $memberToken->created_at = '2021-05-31 22:49:20';
        $memberToken->updated_at = '2021-05-31 22:49:20';
        $dataJson = new AuthResource($memberToken);
        $returnData = ResponseFormatter::jsonFormate($dataJson , ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        //validator 回傳什麼 controller 就回傳什麼
        /******設定方法及回傳參數*******/
        $this->authValidatorMock->shouldReceive('login');
        $this->authServiceMock->shouldReceive('login')->andReturn($member);
        $this->memberTokenServiceMock->shouldReceive('getTokenByMemberId')->andReturn($memberToken);
        $this->assertEquals($this->authController->login($request), $returnData);
    }
}
