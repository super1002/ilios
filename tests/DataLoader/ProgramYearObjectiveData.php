<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramYearObjectiveDTO;

class ProgramYearObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 1',
            'programYear' => '1',
            'terms' => ['2', '4'],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'ancestor' => null,
            'descendants' => [],
            'competency' => null,
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 2',
            'programYear' => '5',
            'terms' => ['2'],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'ancestor' => null,
            'descendants' => [],
            'competency' => null,
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 3',
            'programYear' => '2',
            'terms' => [],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'ancestor' => null,
            'descendants' => [],
            'competency' => null,
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return ProgramYearObjectiveDTO::class;
    }
}
