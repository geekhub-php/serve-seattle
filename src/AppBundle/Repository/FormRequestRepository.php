<?php

namespace AppBundle\Repository;

use AppBundle\Entity\DTO\Filter;

/**
 * FormRequestRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormRequestRepository extends \Doctrine\ORM\EntityRepository
{
    public function selectRequestFormsByParams(Filter $filter)
    {
        $query = $this->createQueryBuilder('f')
            ->leftJoin('f.user', 'u')
            ->orderBy('f.createdAt', 'DESC')
        ;

        if ($filter->type && $filter->type != 'All') {
            $query->andWhere('f.type = ?1')
                ->setParameter('1', $filter->type)
            ;
        }

        if ($filter->decision && $filter->decision != 'All') {
            $query->andWhere('f.status = ?2')
                ->setParameter('2', $filter->decision)
            ;
        }

        if ($filter->start && $filter->end) {
            $query->andWhere('f.createdAt BETWEEN ?3 AND ?4')
                    ->setParameter('3', $filter->getStart())
                    ->setParameter('4', $filter->getEnd())
                ;
        }

        return $query->getQuery();
    }

    public function selectLastRequestForms()
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.status = ?1')
            ->setParameter('1', 'pending')
            ->orderBy('r.updatedAt', 'DESC')
            ->setMaxResults(5);

        return $query->getQuery()->getResult();
    }
}
