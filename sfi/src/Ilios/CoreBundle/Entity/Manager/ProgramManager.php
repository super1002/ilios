<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Program manager service.
 * Class ProgramManager
 * @package Ilios\CoreBundle\Manager
 */
class ProgramManager implements ProgramManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramInterface
     */
    public function findProgramBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ProgramInterface[]|Collection
     */
    public function findProgramsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramInterface $program
     * @param bool $andFlush
     */
    public function updateProgram(
        ProgramInterface $program,
        $andFlush = true
    ) {
        $this->em->persist($program);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramInterface $program
     */
    public function deleteProgram(
        ProgramInterface $program
    ) {
        $this->em->remove($program);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return ProgramInterface
     */
    public function createProgram()
    {
        $class = $this->getClass();
        return new $class();
    }
}
