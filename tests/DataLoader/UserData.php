<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\UserDTO;

class UserData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'lastName' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->firstName,
            'displayName' => $this->faker->name,
            'email' => $this->faker->email,
            'preferredEmail' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '1111@school.edu',
            'userSyncIgnore' => false,
            'addedViaIlios' => false,
            'examined' => true,
            'root' => false,
            'icsFeedKey' => hash('sha256', '1'),
            'reports' => [],
            'school' => '1',
            'authentication' => '1',
            'primaryCohort' => '1',
            'directedCourses' => ['1'],
            'learnerGroups' => [],
            'instructedLearnerGroups' => ['1', '3'],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => ["6", "8"],
            'programYears' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'pendingUserUpdates' => ['1'],
            'directedSchools' => ['1'],
            'administeredSchools' => ['1'],
            'administeredSessions' => ['1'],
            'administeredCourses' => ['1'],
            'directedPrograms' => ['1'],
            'administeredCurriculumInventoryReports' => ['1'],
        ];

        $arr[] = [
            'id' => 2,
            'lastName' => 'first',
            'middleName' => 'first',
            'firstName' => 'first',
            'displayName' => 'disnom',
            'email' => 'first@example.com',
            'preferredEmail' => $this->faker->email,
            'phone' => '415-555-0123',
            'enabled' => true,
            'campusId' => '2222@school.edu',
            'userSyncIgnore' => true,
            'addedViaIlios' => true,
            'examined' => true,
            'icsFeedKey' => hash('sha256', '2'),
            'root' => true,
            'reports' => ['1', '2', '3'],
            'school' => '1',
            'authentication' => '2',
            'directedCourses' => ['2', '4'],
            'learnerGroups' => ['1', '2', '3'],
            'instructedLearnerGroups' => [],
            'instructorGroups' => ['1', '2', '3'],
            'instructorIlmSessions' => ['3'],
            'learnerIlmSessions' => ['4'],
            'offerings' => ['4'],
            'instructedOfferings' => ['5'],
            'programYears' => [],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'pendingUserUpdates' => [],
            'directedSchools' => [],
            'administeredSchools' => [],
            'administeredSessions' => [],
            'administeredCourses' => [],
            'directedPrograms' => [],
            'administeredCurriculumInventoryReports' => [],
        ];

        $arr[] = [
            'id' => 3,
            'lastName' => 'second',
            'middleName' => $this->faker->firstName,
            'firstName' => 'second',
            'email' => 'second@example.com',
            'preferredEmail' => 'zweite@example.de',
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '3333@school.edu',
            'otherId' => '001',
            'userSyncIgnore' => false,
            'addedViaIlios' => false,
            'examined' => false,
            'icsFeedKey' => hash('sha256', '3'),
            'root' => false,
            'reports' => [],
            'school' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructedLearnerGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'roles' => ['2'],
            'cohorts' => [],
            'pendingUserUpdates' => [],
            'directedSchools' => [],
            'administeredSchools' => [],
            'administeredSessions' => [],
            'administeredCourses' => [],
            'directedPrograms' => [],
            'administeredCurriculumInventoryReports' => [],
        ];

        $arr[] = [
            'id' => 4,
            'lastName' => $this->faker->lastName,
            'middleName' => $this->faker->firstName,
            'firstName' => $this->faker->firstName,
            'displayName' => $this->faker->name,
            'email' => $this->faker->email,
            'preferredEmail' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '4444@school.edu',
            'userSyncIgnore' => false,
            'addedViaIlios' => false,
            'examined' => false,
            'icsFeedKey' => hash('sha256', '4'),
            'root' => false,
            'reports' => [],
            'school' => "2",
            'directedCourses' => ["3"],
            'learnerGroups' => [],
            'instructedLearnerGroups' => [],
            'instructorGroups' => ['2'],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'roles' => [],
            'cohorts' => [],
            'pendingUserUpdates' => ['2'],
            'directedSchools' => [],
            'administeredSchools' => [],
            'administeredSessions' => [],
            'administeredCourses' => ['5'],
            'directedPrograms' => [],
            'administeredCurriculumInventoryReports' => [],
        ];

        $arr[] = [
            'id' => 5,
            'lastName' => $this->faker->lastName,
            'middleName' => $this->faker->firstName,
            'firstName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'preferredEmail' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '5555@school.edu',
            'userSyncIgnore' => false,
            'addedViaIlios' => false,
            'examined' => false,
            'icsFeedKey' => hash('sha256', '5'),
            'root' => false,
            'reports' => [],
            'school' => "1",
            'directedCourses' => [],
            'learnerGroups' => ['1', '5'],
            'instructedLearnerGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => ['7'],
            'instructedOfferings' => [],
            'programYears' => [],
            'roles' => [],
            'cohorts' => [],
            'pendingUserUpdates' => [],
            'directedSchools' => [],
            'administeredSchools' => [],
            'administeredSessions' => [],
            'administeredCourses' => [],
            'directedPrograms' => [],
            'administeredCurriculumInventoryReports' => [],
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 6,
            'lastName' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'preferredEmail' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '5555@school.edu',
            'userSyncIgnore' => false,
            'addedViaIlios' => true,
            'examined' => false,
            'icsFeedKey' => hash('sha256', microtime()),
            'root' => false,
            'reports' => [],
            'school' => "1",
            'directedCourses' => ['1'],
            'learnerGroups' => ['1'],
            'instructedLearnerGroups' => ['1'],
            'instructorGroups' => ['1'],
            'instructorIlmSessions' => ['1'],
            'learnerIlmSessions' => ['1'],
            'offerings' => ['1'],
            'instructedOfferings' => ['1'],
            'programYears' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'pendingUserUpdates' => [],
            'directedSchools' => [],
            'administeredSchools' => [],
            'administeredSessions' => [],
            'administeredCourses' => [],
            'directedPrograms' => [],
            'administeredCurriculumInventoryReports' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, UserDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
