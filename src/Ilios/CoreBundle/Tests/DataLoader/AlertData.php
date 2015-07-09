<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AlertData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
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
