<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Permission API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class PermissionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'permissions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'user' => ['user', 3],
            'canRead' => ['canRead', false],
            'canWrite' => ['canWrite', false],
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text(30)],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'user' => [[0, 1, 2, 3], ['user' => 2]],
            'canRead' => [[0, 1, 3], ['canRead' => true]],
            'canNotRead' => [[2], ['canRead' => false]],
            'canWrite' => [[0, 2, 3], ['canWrite' => true]],
            'canNotWrite' => [[1], ['canWrite' => false]],
            'tableRowId' => [[1, 2], ['tableRowId' => 1]],
            'tableName' => [[0, 3], ['tableName' => 'school']],
        ];
    }

    public function testPostMany()
    {
        $userDataLoader = $this->container->get('ilioscore.dataloader.user');
        $users = $userDataLoader->createMany(51);
        $savedUsers = $this->postMany('users', 'users', $users);

        $dataLoader = $this->getDataLoader();

        $data = [];
        foreach ($savedUsers as $i => $user) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['user'] = (string) $user['id'];

            $data[] = $arr;
        }


        $this->postManyTest($data);
    }
}
