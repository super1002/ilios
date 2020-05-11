<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Entity\CourseObjectiveInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Entity\SessionObjectiveInterface;
use Doctrine\Common\Collections\ArrayCollection;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\Objective as Voter;
use App\Service\PermissionChecker;
use App\Entity\Course;
use App\Entity\Objective;
use App\Entity\ObjectiveInterface;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\Session;
use App\Entity\School;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ObjectiveTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(ObjectiveInterface::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanCreateObjective()
    {
        $token = $this->createMockTokenWithSessionUserPerformingNonLearnerFunction();
        $entity = m::mock(Objective::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateObjective()
    {
        $token = $this->createMockTokenWithSessionUserPerformingOnlyLearnerFunction();
        $entity = m::mock(Objective::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanEditProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection([$programYearObjective]));
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $programYearObjective->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection([$programYearObjective]));
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $programYearObjective->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $programYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection([$programYearObjective]));
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $programYearObjective->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection([$programYearObjective]));
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $programYearObjective->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanEditCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection([$courseObjective]));
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $courseObjective->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection([$courseObjective]));
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $courseObjective->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection([$courseObjective]));
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $courseObjective->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection([$courseObjective]));
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection());
        $courseObjective->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanEditSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection([$sessionObjective]));
        $sessionObjective->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection([$sessionObjective]));
        $sessionObjective->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection([$sessionObjective]));
        $sessionObjective->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYearObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourseObjectives')
            ->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection([$sessionObjective]));
        $sessionObjective->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create allowed");
    }
}
