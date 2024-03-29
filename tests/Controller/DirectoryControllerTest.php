<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Classes\SessionUserInterface;
use App\Repository\UserRepository;
use App\Service\PermissionChecker;
use App\Entity\DTO\UserDTO;
use App\Entity\UserInterface;
use App\Service\Directory;
use App\Controller\DirectoryController;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DirectoryControllerTest extends TestCase
{
    /**
     * @var DirectoryController
     */
    protected $directoryController;

    /**
     * @var m\MockInterface
     */
    protected $tokenStorageMock;

    /**
     * @var m\MockInterface
     */
    protected $userRepositoryMock;

    /**
     * @var m\MockInterface
     */
    protected $directoryMock;

    /**
     * @var m\MockInterface
     */
    protected $permissionChecker;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokenStorageMock = m::mock(TokenStorageInterface::class);
        $this->userRepositoryMock = m::mock(UserRepository::class);
        $this->directoryMock = m::mock(Directory::class);
        $this->permissionChecker = m::mock(PermissionChecker::class);

        $mockSessionUser = m::mock(SessionUserInterface::class);

        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockSessionUser);

        $this->tokenStorageMock->shouldReceive('getToken')->andReturn($mockToken);

        $this->directoryController = new DirectoryController(
            $this->tokenStorageMock,
            $this->userRepositoryMock,
            $this->directoryMock,
            $this->permissionChecker
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->directoryController);
        unset($this->tokenStorageMock);
        unset($this->userRepositoryMock);
        unset($this->directoryMock);
        unset($this->permissionChecker);
    }

    public function testSearchOne()
    {
        $fakeDirectoryUser = [
            'user' => 1,
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->directoryMock
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser]);

        $this->permissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);


        $user = m::mock(UserDTO::class);
        $user->id = 1;
        $user->campusId = 'abc';


        $this->userRepositoryMock
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc'])->andReturn([$user]);


        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->search($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$fakeDirectoryUser]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testSearchReturnsCurrentUserId()
    {
        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'alast',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $fakeDirectoryUser2 = [
            'firstName' => 'first',
            'lastName' => 'xlast',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => '1111@school.edu',
        ];

        $this->directoryMock
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);

        $this->permissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);


        $user = m::mock(UserDTO::class);
        $user->id = 1;
        $user->campusId = '1111@school.edu';

        $this->userRepositoryMock
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc', '1111@school.edu'])->andReturn([$user]);

        $fakeDirectoryUser1['user'] = null;
        $fakeDirectoryUser2['user'] = 1;

        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->search($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));
        $results = json_decode($content, true)['results'];

        $this->assertEquals(
            $fakeDirectoryUser1,
            $results[0],
            var_export($results, true)
        );

        $this->assertEquals(
            $fakeDirectoryUser2,
            $results[1],
            var_export($results, true)
        );
    }

    public function testFind()
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->permissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);

        $this->directoryMock
            ->shouldReceive('findByCampusId')
            ->with('abc')
            ->once()
            ->andReturn($fakeDirectoryUser);

        $userMock = m::mock(UserInterface::class)
            ->shouldReceive('getCampusId')
            ->andReturn('abc')
            ->mock();

        $this->userRepositoryMock
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])->andReturn($userMock);

        $response = $this->directoryController->find(1);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['result' => $fakeDirectoryUser],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testFindFailsIfUserDoesntHaveProperPermissions()
    {
        $this->permissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->directoryController->find(1);
    }

    public function testSearchFailsIfUserDoesntHaveProperPermissions()
    {
        $this->permissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);
        $this->directoryController->search($request);
    }
}
