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

class AuthServiceTest extends TestCase
{
    //要注入的物件
    private $memberServiceMock;
    //service
    private $authService;
    public function __construct()
    {
        parent :: __construct();
        $this->memberServiceMock = \Mockery::mock(MemberService::class);
        $this->authService = new AuthService($this->memberServiceMock);
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
}
