<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\SyncAllUsersCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Common\Collections\ArrayCollection;

use Mockery as m;

class SyncAllUsersCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:directory:sync-users';
    
    protected $userManager;
    protected $authenticationManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    protected $em;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManagerInterface');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');
        $this->em = m::mock('Doctrine\Orm\EntityManager');
        
        $command = new SyncAllUsersCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->directory,
            $this->em
        );
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
        
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->directory);
        unset($this->em);
        unset($this->commandTester);
        m::close();
    }
    
    public function testExecuteUserWithNoChanges()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUsername')->andReturn('username')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Comparing User #42 first last \(email\) to directory user by Campus ID abc/',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithFirstNameChange()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setFirstName')->with('new-first')
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating first name from "first" to "new-first"/',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithLastNameChange()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'new-last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setLastName')->with('new-last')
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating last name from "last" to "new-last"/',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithPhoneNumberChange()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'new-phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setPhone')->with('new-phone')
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating phone number from "phone" to "new-phone"/',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithUsernameChange()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'new-abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->shouldReceive('setUsername')->with('new-abc123')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
                
        $this->authenticationManager->shouldReceive('updateAuthentication')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating username from "abc123" to "new-abc123"/',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    
    public function testExecuteWithNoAuthenticationDataChange()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')->with($user)
            ->shouldReceive('setUsername')->with('abc123')
            ->shouldReceive('getUsername')->andReturn('')
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();
                
        $this->authenticationManager->shouldReceive('createAuthentication')->andReturn($authentication)->once();
        $this->authenticationManager->shouldReceive('updateAuthentication')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating username from "" to "abc123"/',
            $output
        );
        $this->assertRegExp(
            '/User had no Authentication data, creating it now\./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithMultipleUserMatches()
    {
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user1 = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $user2 = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(11)
            ->shouldReceive('getFirstAndLastName')->andReturn('other guy')
            ->shouldReceive('getFirstName')->andReturn('other')
            ->shouldReceive('getLastName')->andReturn('guy')
            ->shouldReceive('getEmail')->andReturn('other-guy')
            ->shouldReceive('getPhone')->andReturn('')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user1, $user2]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user1, false)->once();
        $this->userManager->shouldReceive('updateUser')->with($user2, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Multiple accounts exist for the same Campus ID \(abc\)\.  None of them will be updated\./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyFirstName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => '',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/firstName is required and it is missing from record with Campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyLastName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => '',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/lastName is required and it is missing from record with Campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyEmailName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => '',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/email is required and it is missing from record with Campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyUsernamelName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(new ArrayCollection(['abc']));
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => '',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findUsersBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn(new ArrayCollection([$user]))
            ->once();
        $this->userManager->shouldReceive('updateUser')->with($user, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/username is required and it is missing from record with Campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed Sync Process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
}
