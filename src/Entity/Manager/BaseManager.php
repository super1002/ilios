<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use App\Entity\Repository\DTORepositoryInterface;

/**
 * Class BaseManager
 */
class BaseManager implements ManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var DTORepositoryInterface
     */
    protected $repository;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     */
    public function __construct(ManagerRegistry $registry, $class)
    {
        $this->registry   = $registry;
        $this->em         = $registry->getManagerForClass($class);
        $this->class      = $class;
    }

    /**
     * Get the repository from the registry
     * We have to do this here because the call to registry::getRepository
     * requires the database to be setup which is a problem for using managers in console commands
     *
     * @throws \Exception
     * @return DTORepositoryInterface
     */
    protected function getRepository(): DTORepositoryInterface
    {
        if (!$this->repository) {
            $this->repository = $this->registry->getRepository($this->class);

            if (!$this->repository instanceof DTORepositoryInterface) {
                throw new \Exception(get_class($this->repository) . ' is not a DTORepository');
            }
        }

        return $this->repository;
    }

    /**
     * @inheritdoc
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @inheritdoc
     */
    public function flushAndClear()
    {
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(
        array $criteria
    ) {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findOneById($id): ?object
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findDTOBy(array $criteria)
    {
        $results = $this->getRepository()->findDTOsBy($criteria, null, 1);
        return empty($results) ? false : $results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function update(
        $entity,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($entity);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($entity));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        $entity
    ) {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        return new $class();
    }
}
