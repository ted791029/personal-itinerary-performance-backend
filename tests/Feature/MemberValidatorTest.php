<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\MemberService;
use App\Formatter\ResponseFormatter;
use App\Formatter\ResponseCodeInfo;
use App\Validator\MemberValidator;
use Illuminate\Http\Request;

class MemberValidatorTest extends TestCase
{
    //要注入的物件
    private $memberServiceMock;
    //validator
    private $memberValidator;
    public function __construct()
    {
        parent :: __construct();
        $this->memberServiceMock = \Mockery::mock(MemberService::class);
        $this->memberValidator = new MemberValidator($this->memberServiceMock);
    }   
    /**
     *寄驗證信-輸入參數數量不對
     *
     * @return void
     */
    public function testSendVerificationCodeByInputsLengthError()
    {
        /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'test' => 'test',
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->memberValidator->sendVerificationCode($request), $returnData);
    }
    /**
     *寄驗證信-輸入參數有空值
     *
     * @return void
     */
    public function testSendVerificationCodeByInputsHasNull()
    {
         /******建立需要的參數*******/
        $request = Request::create('api/Member/sendVerificationCode', 'POST', array(
            'memberToken' => null,
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->memberValidator->sendVerificationCode($request), $returnData);
    }
     /**
     *寄驗證信-輸入參數數量不對
     *
     * @return void
     */
    public function testSendVerifyByInputsLengthError()
    {
         /******建立需要的參數*******/
         $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => '123456',
            'test' => 'test',
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_LENGTH_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->memberValidator->verify($request), $returnData);
    }
    /**
     *寄驗證信-輸入參數有空值
     *
     * @return void
     */
    public function testSendVerifyByInputsHasNull()
    {
         /******建立需要的參數*******/
         $request = Request::create('api/Member/verify', 'POST', array(
            'memberToken' => '11b5c5f0000000005499ec42908352bd',
            'verificationCode' => null,
        ));
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_PARAM_ERROR_CODE, ResponseCodeInfo::$RESPONSE_PARAM_ERROR_MSG);
        /******設定方法及回傳參數*******/
        $this->assertEquals($this->memberValidator->verify($request), $returnData);
    }
}
