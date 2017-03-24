<?php

namespace AppBundle\Repository;

use AppBundle\Entity\DTO\SurveyFilter;

/**
 * SurveyRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SurveyRepository extends \Doctrine\ORM\EntityRepository
{
    public function findSurveyByStatus($status)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->orderBy('s.updatedAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function findSurveyByParams(SurveyFilter $parameters)
    {
        $type = $parameters->getType();
        $start = $parameters->getStart();
        $endDay = $parameters->getEnd();
        $end = date_time_set($endDay, 23, 59, 59);

        $query = $this->createQueryBuilder('s')
            ->where('s.type = :type')
            ->andWhere('s.updatedAt >= :start')
            ->andWhere('s.updatedAt <= :end')
            ->andWhere('s.status = :status')
            ->setParameters(array('type' => $type, 'start' => $start, 'end' => $end, 'status' => 'submited'))
            ->orderBy('s.updatedAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    public function findSurveyByUser($user)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.updatedAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
