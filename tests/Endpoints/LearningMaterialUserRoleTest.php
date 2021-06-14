<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialUserRoleData;
use App\Tests\ReadEndpointTest;

/**
 * LearningMaterialUserRole API endpoint Test.
 * @group api_1
 */
class LearningMaterialUserRoleTest extends ReadEndpointTest
{
    protected string $testName =  'learningMaterialUserRoles';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            LoadLearningMaterialUserRoleData::class,
            LoadLearningMaterialData::class
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
            'title' => [[1], ['title' => 'second lm user role']],
        ];
    }
}
