<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\Vocabulary;
use App\Entity\DTO\VocabularyDTO;
use Doctrine\Persistence\ManagerRegistry;

class VocabularyRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vocabulary::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\Vocabulary', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('App\Entity\Vocabulary', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var VocabularyDTO[] $vocabularyDTOs */
        $vocabularyDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $vocabularyDTOs[$arr['id']] = new VocabularyDTO(
                $arr['id'],
                $arr['title'],
                $arr['active']
            );
        }
        $vocabularyIds = array_keys($vocabularyDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId'
            )
            ->from('App\Entity\Vocabulary', 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $vocabularyIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $vocabularyDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $related = [
            'terms',
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS vocabularyId')
                ->from('App\Entity\Vocabulary', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $vocabularyIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $vocabularyDTOs[$arr['vocabularyId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($vocabularyDTOs);
    }


    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        $related = [
            'terms',
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_${rel}";
                $param = ":${rel}";
                $qb->join("x.${rel}", $alias);
                $qb->andWhere($qb->expr()->in("${alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `vocabulary_id`,`title`,`school_id`, `active`
        $entity = new Vocabulary();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($referenceMap['school' . $data[2]]);
        $entity->setActive((bool) $data[3]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
