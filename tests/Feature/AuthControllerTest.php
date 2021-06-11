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

class AuthControllerTest extends TestCase
{
    /**
     * 帳號是否存在-存在
     *
     * @return void
     */
    public function testIsAccountExitByExit()
    {
        $authServiceMock = \Mockery::mock(AuthService::class);
        $authValidatorMock = \Mockery::mock(AuthValidator::class);
        $memberTokenServiceMock = \Mockery::mock(MemberTokenService::class);
        $account = 'azoocx791029@gmail.com';
        $member = [
            'id' => '1',
            'account' => 'azoocx791029@gmail.com',
            'name' => 'Ted',
            'verifyStatus' => '0'
        ];
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_CODE, ResponseCodeInfo::$RESPONSE_MEMBER_ISREGISTER_ERROR_MSG);
        $authServiceMock->shouldReceive('isAccountExit')->andReturn($member);
        $authController = new AuthController($authServiceMock, $authValidatorMock, $memberTokenServiceMock);
        $this->assertEquals($authController->isAccountExit($account), $returnData);
    }
    
    /**
     * 帳號是否存在-不存在
     *
     * @return void
     */
    public function testIsAccountExitByNotExit()
    {
        $authServiceMock = \Mockery::mock(AuthService::class);
        $authValidatorMock = \Mockery::mock(AuthValidator::class);
        $memberTokenServiceMock = \Mockery::mock(MemberTokenService::class);
        $account = 'azoocx7910291@gmail.com';
        $member = null;
        $returnData = ResponseFormatter::jsonFormate("", ResponseCodeInfo::$RESPONSE_SUCESS_CODE, ResponseCodeInfo::$RESPONSE_SUCESS_MSG);
        $authServiceMock->shouldReceive('isAccountExit')->andReturn($member);
        $authController = new AuthController($authServiceMock, $authValidatorMock, $memberTokenServiceMock);
        $this->assertEquals($authController->isAccountExit($account), $returnData);
    }
}
