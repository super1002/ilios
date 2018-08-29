<?php

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AamcResourceTypeInterface;
use AppBundle\Entity\TermInterface;

/**
 * Class LoadTermAamcResourceTypeTest
 */
class LoadTermAamcResourceTypeTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'AppBundle\Entity\Manager\TermManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadTermAamcResourceTypeData',
        ];
    }

    /**
     * @covers \AppBundle\DataFixtures\ORM\LoadTermAamcResourceTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('term_x_aamc_resource_type.csv');
    }

    /**
     * @param array $data
     * @param TermInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `term_id`,`resource_type_id`
        $this->assertEquals($data[0], $entity->getId());
        // find the AAMC resource type
        $resourceId = $data[1];
        $method = $entity->getAamcResourceTypes()->filter(
            function (AamcResourceTypeInterface $resourceType) use ($resourceId) {
                return $resourceType->getId() === $resourceId;
            }
        )->first();
        $this->assertNotEmpty($method);
    }
}
