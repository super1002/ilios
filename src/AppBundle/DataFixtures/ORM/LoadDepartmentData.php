<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use AppBundle\Entity\Department;
use AppBundle\Entity\DepartmentInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Class LoadDepartmentData
 */
class LoadDepartmentData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('department');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @return DepartmentInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Department();
    }

    /**
     * @param DepartmentInterface $entity
     * @param array $data
     * @return IdentifiableEntityInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `department_id`,`title`,`school_id`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        return $entity;
    }
}
