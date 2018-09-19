<?php

namespace Tests\App\Fixture;

use App\Entity\Authentication;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAuthenticationData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\App\DataLoader\AuthenticationData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Authentication();
            $entity->setUsername($arr['username']);
            $entity->setPasswordSha256($arr['passwordSha256']);
            $entity->setPasswordBcrypt($arr['passwordBcrypt']);
            $entity->setUser($this->getReference('users' . $arr['user']));

            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\App\Fixture\LoadUserData'
        );
    }
}
