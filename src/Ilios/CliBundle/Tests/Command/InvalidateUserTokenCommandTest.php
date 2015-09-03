<?php

use Ilios\CliBundle\Command\InvalidateUserTokenCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use \DateTime;

class InvalidateUserTokenCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:setup:invalidate-user-tokens';
    
    protected $userManager;
    protected $authenticationManager;
    protected $CommandTester;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManagerInterface');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        
        $command = new InvalidateUserTokenCommand($this->userManager, $this->authenticationManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->commandTester);
        m::close();
    }
    
    public function testHappyPathExecute()
    {
        $now = new DateTime();
        sleep(2);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setInvalidateTokenIssuedBefore')->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userManager->shouldReceive('findUserBy')->with(array('id' => 1))->andReturn($user);
        $this->authenticationManager->shouldReceive('updateAuthentication')->with($authentication);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );
        
        preg_match('/[0-9:APM\s]+ UTC/', $output, $matches);
        $time = trim($matches[0]);
        $since = new DateTime($time);
        $diff = $since->getTimestamp() - $now->getTimestamp();
        $this->assertTrue(
            $diff > 1
        );
    }
    
    public function testNoAuthenticationForUser()
    {
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')
            ->shouldReceive('setInvalidateTokenIssuedBefore')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userManager->shouldReceive('findUserBy')->with(array('id' => 1))->andReturn($user);
        $this->authenticationManager
            ->shouldReceive('createAuthentication')->andReturn($authentication)
            ->shouldReceive('updateAuthentication')->with($authentication);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );
    }
    
    public function testBadUserId()
    {
        $this->userManager->shouldReceive('findUserBy')->with(array('id' => 1))->andReturn(null);
        $this->setExpectedException('Exception', 'No user with id #1');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
    }
    
    public function testUserRequired()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
