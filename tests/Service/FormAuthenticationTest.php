<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use App\Entity\UserInterface;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;
use App\Service\FormAuthentication;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FormAuthenticationTest extends TestCase
{
    protected $authenticationRepository;
    protected $userRepository;
    protected $hasher;
    protected $tokenStorage;
    protected $jwtManager;
    protected $sessionUserProvider;

    /**
     * @var FormAuthentication
     */
    protected $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->hasher = m::mock(UserPasswordHasherInterface::class);
        $this->tokenStorage = m::mock(TokenStorageInterface::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->userRepository = m::mock(UserRepository::class);
        $this->obj = new FormAuthentication(
            $this->authenticationRepository,
            $this->userRepository,
            $this->hasher,
            $this->tokenStorage,
            $this->jwtManager,
            $this->sessionUserProvider
        );
    }

    protected function tearDown(): void
    {
        unset($this->authenticationRepository);
        unset($this->userRepository);
        unset($this->hasher);
        unset($this->tokenStorage);
        unset($this->jwtManager);
        unset($this->sessionUserProvider);
        unset($this->obj);
    }

    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof FormAuthentication);
    }

    public function testMissingValues()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => null,
            'password' => null
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }

    public function testBadUserName()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn(null);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testBadPassword()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->hasher->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(false);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testDisabledUser()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testSuccess()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $this->hasher->shouldReceive('needsRehash')->with($sessionUser)->andReturn(false);
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->hasher->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(true);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
