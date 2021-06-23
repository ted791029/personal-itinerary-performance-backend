<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Validator\Validator;
use App\Services\MemberService;
use App\Validator\AuthValidator;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Models\Member;

class AuthValidatorTest extends TestCase
{
    //要注入的物件
    private $memberServiceMock;
    //validator
    private $authValidator;
    public function __construct()
    {
        parent :: __construct();
        $this->memberServiceMock = \Mockery::mock(MemberService::class);
        $this->authValidator = new AuthValidator($this->memberServiceMock);
    }    
    /**
     * 註冊-輸入參數數量錯誤
     *
     * @return void
     */
    public function testRegistertByInputsLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd86843555',
            'test'    => 'test'
       ));
        $returnData = ResponseFormatter::jsonFormate('', ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-輸入參數有空值
     *
     * @return void
     */
    public function testRegistertByInputsHasNull()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => ''
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-密碼格式錯誤-密碼長度不夠
     *
     * @return void
     */
    public function testRegistertByPasswordLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'd12345' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-密碼格式錯誤-密碼全數字
     *
     * @return void
     */
    public function testRegistertByPasswordAllNumber()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => '12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-密碼格式錯誤-密碼全英文
     *
     * @return void
     */
    public function testRegistertByPasswordAllChatar()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'aaaaaaaaa' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-密碼格式錯誤-密碼含有其他字符
     *
     * @return void
     */
    public function testRegistertByPasswordHasOtherChatar()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'test@gmail.com',
            'password' => 'a12345678~' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_PASSWORD_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-帳號已經存在
     *
     * @return void
     */
    public function testRegistertByAccountIsExit()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'name' => 'Ted',
            'account'  => 'azoocx791029@gmail.com',
            'password' => 'a12345678' //密碼只由英文和數字組成，並且需大於8碼、至少包含1英文和1數字
        ));
        $member = new Member();
        $member->id = '1';
        $member->name = 'Ted';
        $member->account = 'azoocx791029@gmail.com';
        $member->password = 'd86843555';
        $member->created_at = '2021-05-31 22:49:20';
        $member->updated_at = '2021-05-31 22:49:20';
        $member->verifyStatus = '0';
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn($member);
        $this->assertEquals($this->authValidator->register($request), $returnData);
    }
    /**
     * 註冊-輸入參數數量錯誤
     *
     * @return void
     */
    public function testLoginByInputsLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => 'd86843555',
            'test'    => 'test'
       ));
        $returnData = ResponseFormatter::jsonFormate('', ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->login($request), $returnData);
    }
    /**
     * 註冊-輸入參數有空值
     *
     * @return void
     */
    public function testLoginByInputsHasNull()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/login', 'POST', array(
            'account'  => 'test@gmail.com',
            'password' => ''
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->login($request), $returnData);
    }
    /**
     * 寄忘記密碼驗證信-輸入參數數量錯誤
     *
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/sendForgetPasswordVerificationCode', 'POST', array(
            'account'  => 'test@gmail.com',
            'test' => 'test'
       ));
        $returnData = ResponseFormatter::jsonFormate('', ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->sendForgetPasswordVerificationCode($request), $returnData);
    }
    /**
     * 寄忘記密碼驗證信-輸入參數有空值
     *
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByHasNull()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/sendForgetPasswordVerificationCode', 'POST', array(
            'account'  => null,
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->sendForgetPasswordVerificationCode($request), $returnData);
    }
    /**
     * 寄忘記密碼驗證信-找不到會員
     *
     * @return void
     */
    public function testSendForgetPasswordVerificationCodeByNotFindMember()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/sendForgetPasswordVerificationCode', 'POST', array(
            'account'  => 'test@gmail.com',
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn(null);
        $this->assertEquals($this->authValidator->sendForgetPasswordVerificationCode($request), $returnData);
    }
    /**
     * 忘記密碼驗證碼是否存在-輸入參數數量錯誤
     *
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitByLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/forgetPasswordVerificationCodeIsExit', 'POST', array(
            'account'  => 'test@gmail.com',
            'verificationCode' => '123456',
            'test' => 'test'
       ));
        $returnData = ResponseFormatter::jsonFormate('', ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->forgetPasswordVerificationCodeIsExit($request), $returnData);
    }
    /**
     * 忘記密碼驗證碼是否存在-輸入參數有空值
     *
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitByHasNull()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/forgetPasswordVerificationCodeIsExit', 'POST', array(
            'account'  => 'test@gmail.com',
            'verificationCode' => null,
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->authValidator->forgetPasswordVerificationCodeIsExit($request), $returnData);
    }
    /**
     * 忘記密碼驗證碼是否存在-找不到會員
     *
     * @return void
     */
    public function testForgetPasswordVerificationCodeIsExitByNotFindMember()
    {
        /******建立需要的參數*******/
        $request = Request::create('/api/Auth/forgetPasswordVerificationCodeIsExit', 'POST', array(
            'account'  => 'test@gmail.com',
            'verificationCode' => '123456',
       ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_NOT_FIND_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->memberServiceMock->shouldReceive('getByAccount')->andReturn(null);
        $this->assertEquals($this->authValidator->forgetPasswordVerificationCodeIsExit($request), $returnData);
    }
}
