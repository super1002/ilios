<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadWriteEndpointTest;

/**
 * AssessmentOption API endpoint Test.
 * @group api_4
 */
class AssessmentOptionTest extends ReadWriteEndpointTest
{
    protected $testName =  'assessmentOptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\App\Fixture\LoadAssessmentOptionData',
            'Tests\App\Fixture\LoadSessionTypeData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text(18)],
            'sessionTypes' => ['sessionTypes', [2, 3], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'name' => [[1], ['name' => 'second option']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
        ];
    }

    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $faker = $this->getFaker();
        foreach ($all as $data) {
            $data['name'] = $faker->word;

            $this->putTest($data, $data, $data['id']);
        }
    }
}
