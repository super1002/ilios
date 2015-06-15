<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AlertData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'tableRowId' => 9,
            'tableName' => "course",
            'dispatched' => "1",
            'changeTypes' => ['1', '2'],
            'instigators' => [],
            'recipients' => []
        );

        $arr[] = array(
            'id' => 2,
            'tableRowId' => 9,
            'tableName' => "course",
            'dispatched' => "1",
            'changeTypes' => ['2'],
            'instigators' => [],
            'recipients' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => $this->faker->randomDigit,
            'tableRowId' => $this->faker->randomDigit,
            'tableName' => "course",
            'dispatched' => "1",
            'changeTypes' => [1],
            'instigators' => [],
            'recipients' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'string',
            'changeTypes' => [232452],
            'instigators' => [3234],
            'recipients' => [32434]
        ];
    }
}
