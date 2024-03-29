<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\RolloverCourseCommand;
use App\Entity\Course;
use App\Service\CourseRollover;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class RolloverCourseCommandTest
 * @group cli
 */
class RolloverCourseCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:rollover-course';

    /**
     * @var m\MockInterface
     */

    protected $service;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = m::mock(CourseRollover::class);

        $command = new RolloverCourseCommand($this->service);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
        unset($this->commandTester);
    }

    public function testCommandFailsWithoutArguments()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
        ]);
    }

    public function testCommandPassesArgumentsAndDefaultOptions()
    {
        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ]);

        $defaultOptions = [
            'new-start-date' => null,
            'skip-course-learning-materials' => false,
            'skip-course-objectives' => false,
            'skip-course-terms' => false,
            'skip-course-mesh' => false,
            'skip-sessions' => false,
            'skip-session-learning-materials' => false,
            'skip-session-objectives' => false,
            'skip-session-terms' => false,
            'skip-session-mesh' => false,
            'skip-offerings' => false,
            'skip-instructors' => false,
            'skip-instructor-groups' => false,
            'new-course-title' => null,
        ];

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withArgs([$courseId, $newAcademicYear, $defaultOptions])
            ->once();
    }

    public function testCommandPassesUserProvidedOptions()
    {
        $customOptions = [
            'new-start-date' => '2016-03-12',
            'skip-course-learning-materials' => true,
            'skip-course-objectives' => true,
            'skip-course-terms' => true,
            'skip-course-mesh' => true,
            'skip-sessions' => true,
            'skip-session-learning-materials' => true,
            'skip-session-objectives' => true,
            'skip-session-terms' => true,
            'skip-session-mesh' => true,
            'skip-offerings' => true,
            'skip-instructors' => true,
            'skip-instructor-groups' => true,
            'new-course-title' => 'lorem ipsum',
        ];

        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $commandOptions = [
            'command' => self::COMMAND_NAME,
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ];

        foreach ($customOptions as $name => $value) {
            $commandOptions['--' . $name] = $value;
        }

        $this->commandTester->execute($commandOptions);

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withArgs([$courseId, $newAcademicYear, $customOptions])
            ->once();
    }

    public function testCommandPrintsOutNewCourseIdOnSuccess()
    {
        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ]);

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withAnyArgs()
            ->once();

        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            "This course has been rolled over. The new course id is {$newCourseId}.",
            trim($output)
        );
    }
}
