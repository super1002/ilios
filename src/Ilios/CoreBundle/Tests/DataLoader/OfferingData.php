<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class OfferingData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-09-16T15:00:00+00:00",
            'endDate' => "2014-09-16T17:00:00+00:00",
            'session' => '1',
            'learnerGroups' => ['1'],
            'instructorGroups' => ['1'],
            'learners' => [],
            'instructors' => []
        );

        $arr[] = array(
            'id' => 2,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'session' => '1',
            'learnerGroups' => ['2'],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
        );

        $arr[] = array(
            'id' => 3,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-10-15T15:00:00+00:00",
            'endDate' => "2014-10-15T17:00:00+00:00",
            'session' => '2',
            'learnerGroups' => [],
            'instructorGroups' => ['2'],
            'learners' => [],
            'instructors' => [],
        );

        $arr[] = array(
            'id' => 4,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-11-15T15:00:00+00:00",
            'endDate' => "2014-11-15T17:00:00+00:00",
            'session' => '2',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => ['2'],
            'instructors' => [],
        );

        $arr[] = array(
            'id' => 5,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-12-15T15:00:00+00:00",
            'endDate' => "2014-12-15T17:00:00+00:00",
            'session' => '2',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => ["2"],
        );

        $arr[] = array(
            'id' => 6,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2015-01-15T15:00:00+00:00",
            'endDate' => "2015-02-15T17:00:00+00:00",
            'session' => '3',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => ['1'],
        );

        $arr[] = array(
            'id' => 7,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2015-02-15T15:00:00+00:00",
            'endDate' => "2015-02-15T17:00:00+00:00",
            'session' => '3',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 8,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'session' => '1',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
