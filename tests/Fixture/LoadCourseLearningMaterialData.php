<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Course;
use App\Entity\CourseLearningMaterial;
use App\Entity\LearningMaterial;
use App\Entity\MeshDescriptor;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseLearningMaterialData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\CourseLearningMaterialData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CourseLearningMaterial();
            $entity->setId($arr['id']);
            $entity->setRequired($arr['required']);
            $entity->setPublicNotes($arr['publicNotes']);
            $entity->setNotes($arr['notes']);
            $entity->setPosition($arr['position']);
            if (!is_null($arr['startDate'])) {
                $entity->setStartDate(new DateTime($arr['startDate']));
            }
            if (!is_null($arr['endDate'])) {
                $entity->setEndDate(new DateTime($arr['endDate']));
            }
            $entity->setCourse($this->getReference('courses' . $arr['course'], Course::class));
            $entity->setLearningMaterial(
                $this->getReference(
                    'learningMaterials' . $arr['learningMaterial'],
                    LearningMaterial::class
                )
            );
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            $manager->persist($entity);
            $this->addReference('courseLearningMaterials' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
        ];
    }
}
