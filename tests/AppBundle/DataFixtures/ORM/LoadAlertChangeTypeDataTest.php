<?php

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AlertChangeTypeInterface;

/**
 * Class LoadAlertChangeTypeDataTest
 */
class LoadAlertChangeTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'AppBundle\Entity\Manager\AlertChangeTypeManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadAlertChangeTypeData',
        ];
    }

    /**
     * @covers \AppBundle\DataFixtures\ORM\LoadAlertChangeTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('alert_change_type.csv');
    }

    /**
     * @param array $data
     * @param AlertChangeTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `alert_change_type_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }
}
